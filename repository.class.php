<?php // $Id$

/**
 * repository_nanogong
 * Moodle user can record/play nanogong audio/video items
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class repository_nanogong extends repository {
    /*
     * Begin of File picker API implementation
     */
    public function __construct($repositoryid, $context = SITEID, $options = array()) {
        global $action, $itemid;
        parent::__construct ($repositoryid, $context, $options);
        if ('upload' == $action) {
            $this->info = repository::store_to_filepool('repo_upload_file', 'user_draft', '/', $itemid, null, true);
        }
    }
    
    public static function get_type_option_names() {
    	return array('audio_format', 'sampling_rate');
    }
    
    public function type_config_form(&$mform) {
        $audio_format_options = array(
        	get_string('audio_format_imaadpcm', 'repository_nanogong'),
        	get_string('audio_format_speex', 'repository_nanogong'),
        );
        $sampling_rate_options = array(
        	get_string('sampling_rate_low', 'repository_nanogong'),
        	get_string('sampling_rate_medium', 'repository_nanogong'),
        	get_string('sampling_rate_normal', 'repository_nanogong'),
        	get_string('sampling_rate_high', 'repository_nanogong'),
        );
        
        $mform->addElement('select', 'audio_format', get_string('audio_format', 'repository_nanogong'), $audio_format_options);
        $mform->addElement('select', 'sampling_rate', get_string('sampling_rate', 'repository_nanogong'), $sampling_rate_options);
        
        $mform->setHelpButton('audio_format', array('audio_format', get_string('audio_format', 'repository_nanogong'), 'repository_nanogong'));
        $mform->setHelpButton('sampling_rate', array('sampling_rate', get_string('sampling_rate', 'repository_nanogong'), 'repository_nanogong'));
    }

    /**
     * Method to get the repository content.
     *
     * @param string $path current path in the repository
     * @param string $page current page in the repository path
     * @return array structure of listing information
     */
    public function get_listing($path='', $page='') {
        global $CFG, $action;
        if ('upload' == $action) {
            return $this->info;
        }

        $list = array(
        	'nologin' => true,
        	'nosearch' => true,
        	'dynload' => true,
            'iframe' => $CFG->wwwroot . '/repository/nanogong/record.php?repo_id=' . $this->id,
        );
        return $list;
    }

    /**
     * Returns the suported returns values.
     * 
     * @return string supported return value
     */
    public function supported_return_value() {
        return 'ref_id';
    }

    /**
     * Returns the suported file types
     *
     * @return array of supported file types and extensions.
     */
    public function supported_filetypes() {
        return array('web_audio');
    }
    
    /*
     * End of File picker API implementation
     */
    public function print_recorder() {
        global $CFG, $PAGE;

        $sampling_rates = array(
        	array(8000, 11025, 22050, 44100),
        	array(8000, 16000, 32000, 44100)
        );
        $audio_formats = array('ImaADPCM', 'Speex');
        
        $audio_format = get_config('nanogong', 'audio_format');
        $sampling_rate = get_config('nanogong', 'sampling_rate');
        
        $sampling_rate = $sampling_rates[$audio_format][$sampling_rate];
        $audio_format = $audio_formats[$audio_format];
        
        // we need some JS libraries for AJAX
        require_js(array('yui_yahoo', 'yui_dom', 'yui_event', 'yui_element', 'yui_connection', 'yui_json'));

        $PAGE->requires->js('repository/nanogong/record.js');
        $PAGE->requires->data_for_js('nanogong', array(
            'unexpectedevent' => get_string('unexpectedevent', 'repository_nanogong'),
            'appletnotfound' => get_string('appletnotfound', 'repository_nanogong'),
            'norecordingfound' => get_string('norecordingfound', 'repository_nanogong'),
            'nonamefound' => get_string('nonamefound', 'repository_nanogong')
        ));
        
        echo '<div class="nanogong_container">';
        echo '<form onsubmit="nanogongSubmit(); return false;">';
        echo '<input type="hidden" id="repo_id" name="repo_id" value="', $this->id, '" />';
		echo '<label for="filename">', get_string('name', 'repository_nanogong'),':</label>';
		echo '<input type="text" name="filename" id="filename" /><br />';
		echo '<applet id="nanogong_recorder" name="nanogong_recorder" code="gong.NanoGong" width="180" height="40" archive="', $CFG->httpswwwroot, '/repository/nanogong/nanogong.jar">';
		echo '<param name="AudioFormat" value="', $audio_format, '" />';
		echo '<param name="SamplingRate" value="', $sampling_rate, '" />';
        echo '<p>', get_string('javanotfound', 'repository_nanogong'), '</p>';
		echo '</applet><br /><br />';
		echo '<input type="submit" value="', get_string('save', 'repository_nanogong'),'" />';
		echo '</form>';
		echo '</div>';
    }
}
