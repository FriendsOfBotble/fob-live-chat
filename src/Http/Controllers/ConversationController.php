<?php

namespace FriendsOfBotble\LiveChat\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use FriendsOfBotble\LiveChat\Enums\ConversationStatus;
use FriendsOfBotble\LiveChat\Models\Conversation;
use FriendsOfBotble\LiveChat\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends BaseController
{
    public function index(Request $request)
    {
        $this->pageTitle(trans('plugins/fob-live-chat::live-chat.title'));

        $conversations = Conversation::query()
            ->withCount([
                'messages as unread_count' => function ($query) {
                    $query->where('is_from_admin', false)->where('is_read', false);
                },
            ])
            ->addSelect([
                'last_message' => Message::query()
                    ->select('content')
                    ->whereColumn('conversation_id', 'fob_live_chat_conversations.id')
                    ->latest()
                    ->limit(1),
            ])
            ->orderByDesc('last_message_at')
            ->get();

        $selectedId = $request->input('id', $conversations->first()?->id);
        $selected = $selectedId ? Conversation::query()->with('messages')->find($selectedId) : null;

        if ($selected) {
            $selected->messages()
                ->where('is_from_admin', false)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return view('plugins/fob-live-chat::admin.conversations.index', compact('conversations', 'selected', 'selectedId'));
    }

    public function show(int|string $id, Request $request)
    {
        $conversation = Conversation::query()->with('messages')->findOrFail($id);

        $conversation->messages()
            ->where('is_from_admin', false)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($request->ajax()) {
            return $this
                ->httpResponse()
                ->setData([
                    'chat_panel' => view('plugins/fob-live-chat::admin.conversations.partials.chat-panel', compact('conversation'))->render(),
                    'info_panel' => view('plugins/fob-live-chat::admin.conversations.partials.info-panel', compact('conversation'))->render(),
                    'last_message_id' => $conversation->messages->last()?->id ?? 0,
                    'is_open' => $conversation->status->getValue() === ConversationStatus::OPEN,
                ]);
        }

        $this->pageTitle(trans('plugins/fob-live-chat::live-chat.conversation_with', ['name' => $conversation->visitor_name]));

        return view('plugins/fob-live-chat::admin.conversations.show', compact('conversation'));
    }

    public function reply(int|string $id, Request $request)
    {
        $conversation = Conversation::query()->findOrFail($id);

        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $user = Auth::guard()->user();

        Message::query()->create([
            'conversation_id' => $conversation->id,
            'content' => $request->input('message'),
            'is_from_admin' => true,
            'admin_id' => $user->getKey(),
            'admin_name' => setting('fob_live_chat_admin_name') ?: $user->name,
        ]);

        $conversation->update(['last_message_at' => now()]);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/fob-live-chat::live-chat.reply_sent'));
    }

    public function close(int|string $id)
    {
        $conversation = Conversation::query()->findOrFail($id);

        $conversation->update(['status' => ConversationStatus::CLOSED]);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/fob-live-chat::live-chat.conversation_closed'));
    }

    public function destroy(int|string $id)
    {
        $conversation = Conversation::query()->findOrFail($id);

        $conversation->delete();

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/fob-live-chat::live-chat.conversation_deleted'));
    }

    public function fetchMessages(int|string $id, Request $request)
    {
        $conversation = Conversation::query()->findOrFail($id);
        $afterId = (int) $request->input('after_id', 0);

        $query = $conversation->messages()->orderBy('id', 'asc');

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $messages = $query->get()->map(fn (Message $message) => [
            'id' => $message->id,
            'content' => $message->content,
            'is_from_admin' => $message->is_from_admin,
            'admin_name' => $message->admin_name,
            'created_at' => $message->created_at->format('H:i'),
        ]);

        return $this
            ->httpResponse()
            ->setData(['messages' => $messages]);
    }
}
