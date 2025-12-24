<?php

namespace FriendsOfBotble\LiveChat\Tables;

use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\SelectBulkChange;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EnumColumn;
use Botble\Table\Columns\IdColumn;
use FriendsOfBotble\LiveChat\Enums\ConversationStatus;
use FriendsOfBotble\LiveChat\Models\Conversation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class ConversationTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Conversation::class)
            ->addColumns([
                IdColumn::make(),
                Column::make('visitor_name')
                    ->title(trans('plugins/fob-live-chat::live-chat.visitor_name'))
                    ->alignStart(),
                Column::make('visitor_email')
                    ->title(trans('plugins/fob-live-chat::live-chat.visitor_email'))
                    ->alignStart(),
                Column::make('visitor_phone')
                    ->title(trans('plugins/fob-live-chat::live-chat.visitor_phone'))
                    ->alignStart(),
                EnumColumn::make('status')
                    ->title(trans('core/base::tables.status')),
                Column::make('last_message_at')
                    ->title(trans('plugins/fob-live-chat::live-chat.last_message'))
                    ->dateFormat('H:i d/m/Y'),
                CreatedAtColumn::make(),
            ])
            ->addActions([
                DeleteAction::make()->route('fob-live-chat.conversations.destroy'),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('fob-live-chat.conversations.destroy'),
            ])
            ->addBulkChanges([
                SelectBulkChange::make()
                    ->name('status')
                    ->title(trans('core/base::tables.status'))
                    ->choices(ConversationStatus::labels())
                    ->validate(['required', Rule::in(ConversationStatus::values())]),
            ])
            ->queryUsing(function (Builder $query): void {
                $query
                    ->select([
                        'id',
                        'visitor_name',
                        'visitor_email',
                        'visitor_phone',
                        'status',
                        'last_message_at',
                        'created_at',
                    ])
                    ->latest('last_message_at');
            })
            ->onRowClick(function (Conversation $conversation): string {
                return route('fob-live-chat.conversations.show', $conversation->id);
            });
    }
}
