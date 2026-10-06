<?php
// This file is part of Moodle - http://moodle.org/.

require_once(__DIR__ . '/../../course/moodleform_mod.php');

/**
 * Activity settings form.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_supervideotracker_mod_form extends moodleform_mod {
    /**
     * Defines the activity form.
     *
     * @return void
     */
    public function definition(): void {
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name', 'supervideotracker'), ['size' => 64]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements();

        $mform->addElement('select', 'progressmode', get_string('progressmode', 'supervideotracker'), [
            'simple' => get_string('progressmode_simple', 'supervideotracker'),
            'weighted' => get_string('progressmode_weighted', 'supervideotracker'),
        ]);
        $mform->addHelpButton('progressmode', 'packageprogresshelp', 'supervideotracker');

        $mform->addElement('select', 'completionmode', get_string('completionmode', 'supervideotracker'), [
            'allrequired' => get_string('completionmode_allrequired', 'supervideotracker'),
            'count' => get_string('completionmode_count', 'supervideotracker'),
            'weighted' => get_string('completionmode_weighted', 'supervideotracker'),
        ]);
        $mform->addElement('text', 'completioncount', get_string('completioncount', 'supervideotracker'));
        $mform->setType('completioncount', PARAM_INT);
        $mform->setDefault('completioncount', 1);
        $mform->hideIf('completioncount', 'completionmode', 'neq', 'count');

        $mform->addElement('text', 'completionpercent', get_string('completionpercent', 'supervideotracker'));
        $mform->setType('completionpercent', PARAM_INT);
        $mform->setDefault('completionpercent', 100);
        $mform->hideIf('completionpercent', 'completionmode', 'neq', 'weighted');

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Adds the custom Moodle completion rule.
     *
     * @return array Rule names.
     */
    public function add_completion_rules(): array {
        $mform = $this->_form;
        $mform->addElement(
            'advcheckbox',
            'completiontracked',
            '',
            get_string('completiontracked_desc', 'supervideotracker')
        );
        return ['completiontracked'];
    }

    /**
     * Reports whether the custom completion rule is enabled.
     *
     * @param stdClass $data Form data.
     * @return bool
     */
    public function completion_rule_enabled($data): bool {
        return !empty($data['completiontracked']);
    }

    /**
     * Validates completion thresholds.
     *
     * @param array $data Submitted data.
     * @param array $files Submitted files.
     * @return array Errors.
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        if (($data['completionmode'] ?? '') === 'count' && (int)($data['completioncount'] ?? 0) < 1) {
            $errors['completioncount'] = get_string('err_numeric', 'form');
        }
        if (($data['completionmode'] ?? '') === 'weighted') {
            $percent = (int)($data['completionpercent'] ?? 0);
            if ($percent < 1 || $percent > 100) {
                $errors['completionpercent'] = get_string('percentageerror', 'supervideotracker');
            }
        }
        return $errors;
    }
}
