<?php
// This file is part of Moodle - http://moodle.org/.

namespace mod_supervideotracker\form;

use context_module;
use local_video_bridge\source\manager as source_manager;

/**
 * Form used to create and edit one tracked media item.
 */
final class item_form extends \moodleform {
    /** @var context_module Module context. */
    private context_module $context;

    /**
     * Creates the form.
     *
     * @param context_module $context Module context.
     * @param string|null $action Form action.
     * @param mixed $customdata Custom data.
     */
    public function __construct(context_module $context, $action = null, $customdata = null) {
        $this->context = $context;
        parent::__construct($action, $customdata);
    }

    /**
     * Defines fields.
     *
     * @return void
     */
    protected function definition(): void {
        $mform = $this->_form;
        $manager = new source_manager('source', 'sourceconfig', null);
        $sources = $manager->get_options(['tracking']);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'itemid');
        $mform->setType('itemid', PARAM_INT);

        $mform->addElement('text', 'title', get_string('title', 'supervideotracker'), ['size' => 64]);
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', null, 'required', null, 'client');

        $mform->addElement('textarea', 'description', get_string('shortdescription', 'supervideotracker'), [
            'rows' => 3,
            'cols' => 64,
        ]);
        $mform->setType('description', PARAM_TEXT);

        $mform->addElement('select', 'source', get_string('source', 'supervideotracker'), $sources);
        $mform->addHelpButton('source', 'sourcehelp', 'supervideotracker');
        $mform->addRule('source', null, 'required', null, 'client');
        $manager->add_form_elements($mform, 'source');

        $mform->addElement('advcheckbox', 'required', get_string('required', 'supervideotracker'));
        $mform->setDefault('required', 1);

        $mform->addElement('text', 'minpercent', get_string('minpercent', 'supervideotracker'));
        $mform->setType('minpercent', PARAM_INT);
        $mform->setDefault('minpercent', 100);
        $mform->addRule('minpercent', null, 'required', null, 'client');

        $mform->addElement('text', 'weight', get_string('weight', 'supervideotracker'));
        $mform->setType('weight', PARAM_FLOAT);
        $mform->setDefault('weight', 1);

        $mform->addElement('date_time_selector', 'availablefrom', get_string('availablefrom', 'supervideotracker'), [
            'optional' => true,
        ]);
        $mform->addElement('date_time_selector', 'availableuntil', get_string('availableuntil', 'supervideotracker'), [
            'optional' => true,
        ]);
        $mform->addElement('advcheckbox', 'active', get_string('active', 'supervideotracker'));
        $mform->setDefault('active', 1);

        $this->add_action_buttons();
    }

    /**
     * Adds provider-specific validation and business rules.
     *
     * @param array $data Submitted data.
     * @param array $files Submitted files.
     * @return array
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        $manager = new source_manager('source', 'sourceconfig', null);
        $errors = array_merge($errors, $manager->validation($data, $files));

        $percent = (int)($data['minpercent'] ?? 0);
        if ($percent < 1 || $percent > 100) {
            $errors['minpercent'] = get_string('percentageerror', 'supervideotracker');
        }
        if ((float)($data['weight'] ?? 0) <= 0) {
            $errors['weight'] = get_string('weighterror', 'supervideotracker');
        }
        $from = (int)($data['availablefrom'] ?? 0);
        $until = (int)($data['availableuntil'] ?? 0);
        if ($from && $until && $until <= $from) {
            $errors['availableuntil'] = get_string('availabilityerror', 'supervideotracker');
        }

        $source = clean_param((string)($data['source'] ?? ''), PARAM_PLUGIN);
        if ($source !== '' && !array_key_exists($source, $manager->get_options(['tracking']))) {
            $errors['source'] = get_string('trackingrequired', 'supervideotracker');
        }
        return $errors;
    }
}
