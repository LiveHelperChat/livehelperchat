var LHCOperatorNotifications=function(e){const i=e.public_key;if("serviceWorker"in navigator&&"PushManager"in window){let e;navigator.serviceWorker.register(WWW_DIR_JAVASCRIPT+"notifications/serviceworkerop").then(i=>(i.installing?console.log("Service worker installing"):i.waiting?console.log("Service worker installed"):i.active&&console.log("Service worker active"),console.log("Service Worker registered"),e=i,i.pushManager.getSubscription())).then(o=>{document.getElementById("subscribe-persistent").addEventListener("click",()=>{!async function(e){const o={userVisibleOnly:!0,applicationServerKey:n(i)};try{const i=await e.pushManager.subscribe(o);await fetch(WWW_DIR_JAVASCRIPT+"notifications/subscribeop",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(i),credentials:"same-origin"}),t()}catch(e){console.log(e),alert("Subscription failed: "+e.message)}}(e)})})}function t(){fetch(WWW_DIR_JAVASCRIPT+"notifications/loadsubscriptions",{method:"GET",credentials:"same-origin"}).then(e=>e.text()).then(e=>{document.getElementById("subscriptions").innerHTML=e,lhinst.protectCSFR()}).catch(e=>{console.error("Error loading subscriptions:",e)})}function n(e){const i=(e+"=".repeat((4-e.length%4)%4)).replace(/\-/g,"+").replace(/_/g,"/"),t=window.atob(i),n=new Uint8Array(t.length);for(let e=0;e<t.length;++e)n[e]=t.charCodeAt(e);return n}t()};
//# sourceMappingURL=c76977fbe30e267c224152921b536aca.js.map
