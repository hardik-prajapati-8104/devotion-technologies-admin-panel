<?php

return [
    /*
     * Every model that should participate in the Recycle Bin gets an
     * entry here. Nothing shows up in the bin for a model unless it's
     * listed — this is the single place that controls scope.
     *
     * 'label'      => plural display name shown in the UI
     * 'icon'       => Bootstrap Icons class for the type badge
     * 'title_attr' => the model attribute to show as the item's name
     *                 (or a method name — resolved via ->{$titleAttr}
     *                 first, falling back to a callable if you'd rather
     *                 be explicit, see Task's example below)
     */
    'models' => [
        \App\Models\Country::class => [
            'label' => 'Countries',
            'icon'  => 'bi-globe-americas',
            'title' => 'name',
        ],
        \App\Models\Task::class => [
            'label' => 'Tasks',
            'icon'  => 'bi-card-checklist',
            'title' => 'title',
        ],
        \App\Models\TaskBoard::class => [
            'label' => 'Task Boards',
            'icon'  => 'bi-kanban',
            'title' => 'name',
        ],
        \App\Models\Announcement::class => [
            'label' => 'Announcements',
            'icon'  => 'bi-megaphone',
            'title' => 'title',
        ],
        \App\Models\Notice::class => [
            'label' => 'Notices',
            'icon'  => 'bi-bell',
            'title' => 'title',
        ],
        \App\Models\SupportTicket::class => [
            'label' => 'Support Tickets',
            'icon'  => 'bi-life-preserver',
            'title' => 'subject',
        ],
        // Add more models here as you extend Recyclable to them —
        // nothing else in the Recycle Bin needs to change.
    ],
];
