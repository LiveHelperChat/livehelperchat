import axios from "axios";

class _groupChatSync {

    constructor() {
        this.eventEmitter = new EventEmitter();
        this.chatsSynchro = [];
        this.chatsSynchroMsg = [];
        this.timeoutSync = null;
        this.syncInProgress = false;
    }

    sync() {
        if (this.syncInProgress == true) return;

        this.syncInProgress = true;

        axios.post(WWW_DIR_JAVASCRIPT  + "groupchat/sync/", this.chatsSynchroMsg).then(result => {

            let groupedData = [];

            result.data.result.forEach((chatData) => {
                if (!groupedData[chatData.chat_id]) {
                    groupedData[chatData.chat_id] = {};
                }

                groupedData[chatData.chat_id]["msg"] = chatData;

                let index = this.chatsSynchro.indexOf(chatData.chat_id);
                let parts = this.chatsSynchroMsg[index].split(",");
                parts[1] = chatData.message_id;

                this.chatsSynchroMsg[index] = parts.join(",");
            });

            result.data.result_status.forEach((chatData) => {
                if (!groupedData[chatData.chat_id]) {
                    groupedData[chatData.chat_id] = {};
                }

                groupedData[chatData.chat_id]["status"] = chatData;

                let index = this.chatsSynchro.indexOf(chatData.chat_id);
                let parts = this.chatsSynchroMsg[index].split(",");
                parts[2] = chatData.lgsync;

                this.chatsSynchroMsg[index] = parts.join(",");
            })

            groupedData.forEach((chatData, chatId) => {
                this.eventEmitter.emitEvent('gchat_'+chatId, [chatData]);
            });

            this.syncInProgress = false;
        });
    }

    startSync() {
        clearTimeout(this.timeoutSync);
        if (this.chatsSynchro.length > 0) {
            this.timeoutSync = setInterval(() => { this.sync(); }, 2500);
        }
    }

    addSubscriber(chatId, cb) {
        this.chatsSynchro.push(parseInt(chatId));
        this.chatsSynchroMsg.push(chatId+",0,0");
        this.eventEmitter.addListener('gchat_'+chatId, cb);
        this.startSync();
    }

    removeSubscriber(chatId, cb) {
        var index = this.chatsSynchro.indexOf(parseInt(chatId));
        this.chatsSynchro.splice(index, 1);
        this.chatsSynchroMsg.splice(index, 1);
        this.eventEmitter.removeListener('gchat_'+chatId, cb);
        this.startSync();
    }
};

const groupChatSync = new _groupChatSync();
export { groupChatSync };