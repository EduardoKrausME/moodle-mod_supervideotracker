# Super Video Tracker

Super Video Tracker is a Moodle activity for consolidated tracking of multiple videos as one training or compliance package.

The activity is not a playlist. A playlist is primarily concerned with putting videos in a playable collection and often optimizing “next video” playback. Super Video Tracker is concerned with evidence: which required media each learner started, how much was effectively watched, which requirement was met, what is overdue, and whether the package as a whole can be considered complete.

It is also not a sequence. A sequence primarily models order and prerequisites between steps. Super Video Tracker does not require media to be consumed in order; availability windows can restrict individual items, while the package-level calculation remains independent from playback order.

## Video Bridge dependency

The plugin requires:

- `local_video_bridge >= 2026100604`
- repository: https://github.com/EduardoKrausME/moodle-local_video_bridge

Providers are never implemented inside this module. Every item is represented by
`local_video_bridge\media\config`, and the bridge is responsible for provider adapters,
player configuration, capability validation, resume position, viewing maps, progress and
playback telemetry.

This means one package may mix Moodle upload, Bunny Stream, OTTFlix, YouTube or another
installed provider, provided the source declares reliable `tracking`.

## Data ownership

`mod_supervideotracker` stores only:

- activity/package configuration;
- media catalog items;
- required/optional rules;
- minimum percentages;
- weights;
- availability windows;
- package completion rules.

It intentionally has no learner-progress table. Progress and viewing maps remain in
`local_video_bridge`.

## Progress modes

Simple mode calculates the arithmetic mean of active required items.

Weighted mode calculates:

```
sum(item percentage × item weight) / sum(item weight)
```

Optional videos never reduce package progress.

## Completion modes

Moodle activity completion can be driven by one of three package rules:

1. every active required video reaches its own minimum percentage;
2. at least X active videos reach their own minimum percentage;
3. weighted package progress reaches X%.

The completion state is derived live from Video Bridge data.

## Reports

The teacher report is a learner × media matrix. It uses
`local_video_bridge\progress\manager::get_progress_bulk()` to load the matrix in one
query rather than querying each table cell independently.

Reports respect Moodle activity groups. In separate-groups mode, teachers without
`moodle/site:accessallgroups` are restricted to members of their allowed groups.

The matrix can be exported as CSV, and learner detail shows the progress percentage,
last playback position, last view and the viewing map for every item.

## Files, backup and restore

Bridge-owned Moodle uploads and caption files use each media record id as the File API
`itemid`. Backup stores the item catalog and annotates those bridge file areas. Restore
creates new media records first and remaps the files to the new media ids.

Playback progress is not duplicated into the activity backup.

## Development

The repository includes PHPUnit coverage for package calculations and Behat coverage for
basic activity creation. No scheduled task is registered because the module has no work
that requires cron; status and completion are derived from current bridge data.
