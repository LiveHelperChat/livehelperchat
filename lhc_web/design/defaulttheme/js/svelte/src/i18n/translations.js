let scope = "lhcbo";

const responseTrack = await fetch(WWW_DIR_JAVASCRIPT  + "restapi/lang/"+ scope + '/v6', {
    method: "GET",
    headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        "X-CSRFToken": confLH.csrf_token
    }
}).catch((error) => {
    // Your error is here!
    console.log('Translations could not be loaded!');
});

const data = await responseTrack.json();

export default data;

/*
export async function getTranslations(scope){
    scope = scope || "lhcbo";

    const responseTrack = await fetch(WWW_DIR_JAVASCRIPT  + "restapi/lang/"+ scope, {
        method: "GET",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-CSRFToken": confLH.csrf_token
        }
    }).catch((error) => {
        // Your error is here!
        console.log('Translations could not be loaded!');
    });

    return responseTrack.json();
}*/
