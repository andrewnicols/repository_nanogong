// $Id$

/**
 * @file Javascript file to handle nanogong recording
 */
/**
 * Submits the recorded sound using default form elements 
 */
function nanogongSubmit() {
	// find filepicker objects 
    var repository_client, fp, fp_config, client_id
    if ((repository_client = parent.repository_client) && (fp = repository_client.fp) && (fp_config = parent.fp_config)) {
        for(var cid in fp) {
            if('fs' in fp[cid] && 'iframe' in fp[cid].fs && fp[cid].fs.iframe == window.location) {
                client_id = cid
                fp = fp[cid]
                break;
            }
        }
    }
    if (!client_id) {
    	alert(nanogong['unexpectedevent'] + ' (client_id)')
        return
    }

    // locate the filename
 	var filename
	if (!(filename = document.getElementById('filename')) || !(filename = filename.value)) {
		filename = new Date().toGMTString().replace('+', ' ')
	}

	// submit the sound file
	var ret = nanogongUploadFile('nanogong_recorder', moodle_cfg.wwwroot+'/repository/ws.php?action=upload' + 
		'&repo_id='+fp.fs.repo_id + '&itemid='+fp.itemid + '&ctx_id='+fp_config.contextid + '&client_id='+client_id,
		'repo_upload_file', filename, 'sesskey=' + moodle_cfg.sesskey)
	if(!ret) {
		alert(nanogong['unexpectedevent'] + ' (upload)')
		return;
	}

	// decode the server response
	ret = repository_client.parse_json('' + ret, 'upload')
    if(ret && ret.e) {
    	alert(nanogong['unexpectedevent'] + ' (' + ret.e + ')')
        return;
    }
    
    // select this file and close the file picker
    repository_client.end(client_id, ret);
}

/**
 * Submits the recorded sound using function arguments
 */
function nanogongUploadFile(applet_id, postURL, inputname, filename, cookie) {
    // find nanogong applet
	var recorder
	if (!(recorder = document.getElementById(applet_id)) || !(recorder.sendGongRequest)) {
    	alert(nanogong['appletnotfound'])
	  	return
	}

	// check there is a recording
	var duration = parseInt(recorder.sendGongRequest("GetMediaDuration", "audio")) || 0
	if (duration <= 0) {
	  	alert(nanogong['norecordingfound'])
	  	return
	}

	if (!filename) {
		alert(nanogong['nonamefound'])
		return
	}
	
	// upload the sound file to the server
	return recorder.sendGongRequest('PostToForm', postURL, inputname, cookie, filename)
}
