export async function updateSettings(params) {

    if (params['e']) {
        params['e'].stopPropagation();
        params['e'].preventDefault();
    }

    const responseTrack = await fetch(WWW_DIR_JAVASCRIPT  + params['url'], {
        method: "GET",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-CSRFToken": confLH.csrf_token
        }
    }).catch((error) => {
        // Your error is here!
        alert('We could not change your settings! ' + error);
    });

    const data = await responseTrack.json();

    if (data.error === false) {
        params['store'].update((list) => {list[params['attr']] = !list[params['attr']]; return list;});
    } else if (typeof data.message !== 'undefined') {
        alert(data.message);
    } else if (typeof data.msg !== 'undefined') {
        alert(data.msg);
    } else {
        alert(data);
    }
}
