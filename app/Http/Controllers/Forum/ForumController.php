<?php
namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\{ForumTopic, ForumMessage};
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index()
    {
        $topics = ForumTopic::with('user')
            ->withCount('messages')
            ->latest()
            ->paginate(20);
        return view('forum.index', compact('topics'));
    }

    public function create()
    {
        $this->authorize('create', ForumTopic::class);
        $modules = \App\Models\Module::where('is_published', true)->orderBy('order')->get();
        return view('forum.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', ForumTopic::class);

        $data = $request->validate([
            'title' => 'required|string|min:3|max:150',
            'body'  => 'required|string|min:1|max:5000',
        ], [
            'title.min' => 'Le titre doit contenir au moins 3 caractères.',
            'title.required' => 'Le titre est obligatoire.',
            'body.required' => 'Le message est obligatoire.',
            'body.min' => 'Le message ne peut pas être vide.',
        ]);

        $topic = ForumTopic::create([
            'user_id' => auth()->id(),
            'title'   => strip_tags($data['title']),
            'body'    => strip_tags($data['body']),
        ]);

        return redirect()->route('forum.show', $topic)->with('success', 'Sujet créé.');
    }

    public function show(ForumTopic $topic)
    {
        $messages = $topic->messages()->with('user')->oldest()->paginate(30);
        return view('forum.show', compact('topic', 'messages'));
    }

    public function reply(Request $request, ForumTopic $topic)
    {
        if ($topic->is_locked) {
            return back()->withErrors(['body' => 'Ce sujet est verrouillé.']);
        }

        $data = $request->validate([
            'body' => 'required|string|min:1|max:3000',
        ], [
            'body.required' => 'Le message est obligatoire.',
            'body.min' => 'Le message ne peut pas être vide.',
        ]);

        ForumMessage::create([
            'topic_id' => $topic->id,
            'user_id'  => auth()->id(),
            'body'     => strip_tags($data['body']),
        ]);

        return redirect()->route('forum.show', $topic)->with('success', 'Message envoyé.');
    }

    // ── Modération admin ───────────────────────────────────────────

    public function lock(ForumTopic $topic)
    {
        $this->authorize('lock', $topic);
        $topic->update(['is_locked' => ! $topic->is_locked]);
        $msg = $topic->is_locked ? 'Sujet verrouillé.' : 'Sujet déverrouillé.';
        return back()->with('success', $msg);
    }

    public function destroy(ForumTopic $topic)
    {
        $this->authorize('delete', $topic);
        $topic->messages()->delete();
        $topic->delete();
        return redirect()->route('forum.index')->with('success', 'Sujet supprimé.');
    }

    public function destroyMessage(ForumMessage $message)
    {
        if (auth()->id() !== $message->user_id && ! auth()->user()->isAdmin()) {
            abort(403);
        }
        $message->delete();
        return back()->with('success', 'Message supprimé.');
    }
}
