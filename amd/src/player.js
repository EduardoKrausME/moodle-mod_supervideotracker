// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * player.js
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['local_video_bridge/progress'], function(Progress) {
    const init = (config, rootid) => {
        const root = document.getElementById(rootid);
        if (!root || !config || !config.adaptermodule) {
            return;
        }

        require([config.adaptermodule], (provider) => {
            if (!provider || typeof provider.create !== 'function') {
                return;
            }

            Promise.resolve(provider.create(root, config)).then((adapter) => {
                Progress.attach(adapter, root, config);

                const progress = config.progress || {};
                const position = Number(progress.currenttime || 0);
                const duration = Number(progress.duration || 0);
                const canseek = !config.capabilities || config.capabilities.seeking !== false;
                if (canseek && position > 0 && (!duration || position < duration - 2)
                        && adapter && typeof adapter.seek === 'function') {
                    adapter.seek(position);
                }
                return adapter;
            }).catch(() => {});
        });
    };

    return {init: init};
});
