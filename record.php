<?php
require_once('../../config.php');
require_once($CFG->dirroot.'/repository/lib.php');

global $PAGE;
// we get the request parameters:
// the repository ID controls where the file will be added
$repo_id = required_param('repo_id', PARAM_INT); // repository ID

// load the repository 
$repo = repository::get_instance($repo_id);
if(empty($repo)) {
    die;
}

// we output a simple HTML page with the nanogong.com recording code in it
$PAGE->set_generaltype('popup');
print_header(null, get_string('recordnew', 'repository_nanogong'),null, null, null, false);
?>

<div style="text-align: center;">
<?php $repo->print_recorder() ?>
</div>
<?php
print_footer();
