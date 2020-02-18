module.exports = (function() {

    var vmsg = new require("vmsg");

    const recorder = new vmsg.Recorder({
        wasmURL: window.WWW_DIR_LHC_WEBPACK + "/" + 'vmsg.8c4a15f2.wasm',
        shimURL: "https://unpkg.com/wasm-polyfill.js@0.2.0/wasm-polyfill.js"
    });

    function LHCVoiceMessage() {
            this.recording = null;
            this.audio = null;
            this.chat_id = null;
            this.isRecording = false;
            this.isPlaying = false;
            this.isLoading = false;

            this.audioDuration = 0;
            this.currentTime = 0;

            // Intervals
            this.durationInterval = null;
            this.playInterval = null;
    };

    LHCVoiceMessage.prototype.setStateElement = function(element, state) {
        if (state === true) {
            $('#voice-chat-' + this.chat_id + ' .' + element).show();
        } else {
            $('#voice-chat-' + this.chat_id + ' .' + element).hide();
        }
    }

    LHCVoiceMessage.prototype.updateUIByState = function() {
        this.setStateElement('voice-start-recording',this.isRecording === false);
        this.setStateElement('voice-stop-recording',this.isRecording === true);
        this.setStateElement('voice-play-recording',this.recording !== null && this.isPlaying === false);
        this.setStateElement('voice-stop-play',this.recording !== null && this.isPlaying === true);
        this.setStateElement('voice-send-message',this.recording !== null);

        if (this.isRecording === true || (this.recording !== null && this.isPlaying === false)) {
            $('#voice-chat-' + this.chat_id + ' .voice-audio-status').text(this.audioDuration + 's.');
        } else if (this.isPlaying === true) {
            $('#voice-chat-' + this.chat_id + ' .voice-audio-status').text(this.currentTime + 's.');
        }
        
    }

    LHCVoiceMessage.prototype.startedRecording = function(){
        $('#CSChatMessage-' + this.chat_id).addClass('admin-chat-mic');
        $('#user-chat-status-' + this.chat_id).removeClass('icon-user').addClass('icon-mic');
        $('#user-is-typing-' + this.chat_id).html('Speak now.').css("visibility", "visible");
    }

    LHCVoiceMessage.prototype.stoppedRecording = function(){
        $('#user-chat-status-' + this.chat_id).addClass('icon-user').removeClass('icon-mic');
        $('#CSChatMessage-' + this.chat_id).removeClass('admin-chat-mic');
        $('#user-is-typing-' + this.chat_id).html('');
    }

    LHCVoiceMessage.prototype.startRecording = async function() {

        // Stop playing if it's playing
        this.stopPlayRecord();

        // Reset main attributes
        this.audioDuration = 0;
        this.recording = null;
        this.isPlaying = false;
        this.currentTime = 0;

        try {
            await recorder.initAudio();
            await recorder.initWorker();
            recorder.startRecording();
            this.isRecording = true;

            // Just to update UI
            this.startedRecording();

            this.durationInterval = setInterval(() => {
                this.audioDuration++;
                this.updateUIByState();
            }, 1000);

            this.updateUIByState();

        } catch (e) {
            alert('Sorry but voice messages are not supported on your browser!');
        }
    }

    LHCVoiceMessage.prototype.stopRecording = async function() {
        const blob = await recorder.stopRecording();

        this.recording = blob;
        this.audio = new Audio();
        this.audio.src = URL.createObjectURL(blob);

        this.isRecording = false;

        // Just to update UI
        this.stoppedRecording();

        clearInterval(this.durationInterval);

        this.updateUIByState();
    }

    LHCVoiceMessage.prototype.playRecord = function() {

        this.audio.currentTime = 0;
        this.audio.play();

        this.isPlaying = true;
        this.currentTime = 0;

        this.playInterval = setInterval(
            () => {
                this.currentTime = Math.round(this.audio.currentTime);
                if (this.audio.ended || this.audio.paused) {
                    this.stopPlayRecord();
                }
                this.updateUIByState();
            },
            1000);

        this.updateUIByState();
    }

    LHCVoiceMessage.prototype.stopPlayRecord = function() {

        if (this.isPlaying === true) {
            clearInterval(this.playInterval);
            this.audio.currentTime = 0;
            this.audio.pause();
            this.isPlaying = false;
        }

        this.updateUIByState();
    }

    LHCVoiceMessage.prototype.prepareUIForRecording = function() {

        this.recording = null;
        this.isRecording = false;
        this.isPlaying = false;
        this.isLoading = false;

        $('#voice-chat-' + this.chat_id + ' .go-to-voice').hide();
        $('#voice-chat-' + this.chat_id + ' .voice-ui').html(

            "<i class=\"leave-recording-ui material-icons pointer text-danger mr-0 fs25\" title=\"Cancel\">close</i> | " +
            "<i class=\"voice-start-recording material-icons fs25 pointer text-danger mr-0\" title=\"Start recording\">fiber_manual_record</i>" +
            "<i style=\"display: none\" class=\"voice-stop-recording material-icons fs25 pointer text-danger mr-0\" title=\"Stop recording\">stop</i>" +
            "<i style=\"display: none\" class=\"voice-play-recording material-icons pointer text-success mr-0 fs25\" title=\"Play recorded audio\">play_arrow</i>" +
            "<i style=\"display: none\" class=\"voice-stop-play material-icons pointer text-success mr-0 fs25\" title=\"Stop playing recorded\">stop</i>" +
            "<span class=\"voice-audio-status mr-0 fs11\">0s.</span>" +
            "<span style=\"display: none;\" class=\"ml-1 voice-send-message\" > | <i class=\"material-icons text-success mr-0\" title=\"Send voice message\">send</i></span>"
        );

        var _this = this;

        $('#voice-chat-' + this.chat_id +' .leave-recording-ui').click(function(e){
            _this.leaveVoiceUI();
            e.preventDefault();
            e.stopPropagation();
        });

        $('#voice-chat-' + this.chat_id +' .voice-start-recording').click(function(e){
            _this.startRecording();
            e.preventDefault();
            e.stopPropagation();
        });

        $('#voice-chat-' + this.chat_id +' .voice-stop-recording').click(function(e){
            _this.stopRecording();
            e.preventDefault();
            e.stopPropagation();
        });

        $('#voice-chat-' + this.chat_id +' .voice-play-recording').click(function(e){
            _this.playRecord();
            e.preventDefault();
            e.stopPropagation();
        });

        $('#voice-chat-' + this.chat_id +' .voice-stop-play').click(function(e){
            _this.stopPlayRecord();
            e.preventDefault();
            e.stopPropagation();
        });

        $('#voice-chat-' + this.chat_id +' .voice-send-message').click(function(e){
            _this.sendVoiceMessage();
            e.preventDefault();
            e.stopPropagation();
        });
    }

    LHCVoiceMessage.prototype.sendVoiceMessage = function() {
        const req = new XMLHttpRequest();
        const formData = new FormData();
        formData.append("files[]", this.recording, "record.mp3");
        req.open("POST", WWW_DIR_JAVASCRIPT + '/file/uploadfileadmin/' + this.chat_id);
        req.upload.addEventListener("load", event => {
            this.leaveVoiceUI();

            lhinst.updateChatFiles(this.chat_id);
            lhinst.syncadmincall();

            if (LHCCallbacks.addFileUpload) {
                LHCCallbacks.addFileUpload(this.chat_id);
            }

        });
        req.send(formData);
    }

    LHCVoiceMessage.prototype.leaveVoiceUI = function() {
        $('#voice-chat-' + this.chat_id + ' .go-to-voice').show();
        $('#voice-chat-' + this.chat_id + ' .voice-ui').html('');

        if (this.isRecording) {
            recorder.stopRecording();
        }

        this.stopPlayRecord();
        this.stoppedRecording();

        // Just free up memory
        this.recording = null;
        this.audio = null;
    }

    LHCVoiceMessage.prototype.listen = function(params) {

        this.chat_id = params['chat_id'];

        this.prepareUIForRecording();
    };

    return new LHCVoiceMessage();
})();
