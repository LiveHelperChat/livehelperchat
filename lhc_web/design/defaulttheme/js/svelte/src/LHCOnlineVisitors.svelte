<svelte:options customElement={{
		tag: 'lhc-online-visitors',
		shadow: 'none'}}/>
<script>
    import { lhcOnlineVisitors } from './stores.js';

    import { onMount } from 'svelte';
    import lhcServices from './lib/Services.js';

    let lhcLogic = {
        timeoutVisitors: null,
        department_dpgroups: [],
        department: [],
        timeout: null,
        time_on_site: null,
        country: "none",
        max_rows: 50,
        updateTimeout: 10,
        userTimeout: 3600,
        lhcListRequestInProgress: false,
        timeoutControl: null,
    };

    onMount(async() => {

    });

    function getSyncFilter()
    {
        return "/(method)/ajax/(timeout)/" + lhcLogic.timeout + (lhcLogic.department_dpgroups.length > 0 ? '/(department_dpgroups)/' + lhcLogic.department_dpgroups.join('/') : '' ) + (lhcLogic.department.length > 0 ? '/(department)/' + lhcLogic.department.join('/') : '' ) + (lhcLogic.max_rows > 0 ? '/(maxrows)/' + lhcLogic.max_rows : '' ) + (lhcLogic.country != '' ? '/(country)/' + lhcLogic.country : '' ) + (lhcLogic.time_on_site != '' ? '/(timeonsite)/' + encodeURIComponent(lhcLogic.time_on_site) : '');

    }

    async function syncOnlineVisitors(){
        if (lhcLogic.lhcListRequestInProgress === true) {
            return;
        }

        lhcLogic.lhcListRequestInProgress = true;

        clearTimeout(lhcLogic.timeoutControl);

        try {
            const responseTrack = await fetch(WWW_DIR_JAVASCRIPT  + 'chat/onlineusers' + getSyncFilter(), {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                }
            });

            if (!responseTrack.ok) {
                throw new Error("Network response was not OK [" + responseTrack.status + "] ["+ responseTrack.statusText+"]");
            }

            const data = await responseTrack.json();
            lhcOnlineVisitors.update((list) => {
                list.onlineusers = data.list;
                return list;
            });


            if (lhcLogic.setTimeoutEnabled === true) {
                lhcLogic.timeoutControl = setTimeout(function(){
                    syncOnlineVisitors();
                },confLH.back_office_sinterval);
            }

            lhcLogic.isListLoaded = true;

        } catch (error) {
            $lhcList.lhcConnectivityProblem = true;
            $lhcList.lhcConnectivityProblemExplain = error;
            lhcLogic.lhcListRequestInProgress = false;

            lhcLogic.timeoutControl = setTimeout(function(){
                loadChatList();
            },confLH.back_office_sinterval);

            console.error("There has been a problem with your fetch operation:", error);
        }
    }

</script>