// This file is part of Moodle - http://moodle.org/.
//
// @module mod_supervideotracker/player

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
