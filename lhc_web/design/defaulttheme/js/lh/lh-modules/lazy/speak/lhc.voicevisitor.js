module.exports = (function() {

    var vmsg = new require("vmsg");

    const recorder = new vmsg.Recorder({
        wasmURL: window.WWW_DIR_LHC_WEBPACK + "/" + 'vmsg.8c4a15f2.wasm',
        shimURL: "https://unpkg.com/wasm-polyfill.js@0.2.0/wasm-polyfill.js"
    });

    function LHCVoiceVisitorMessage() {
        this.recording = null;
        this.audio = null;
        this.chat_id = null;
        this.hash = null;
        this.isRecording = false;
        this.isPlaying = false;
        this.isLoading = false;

        this.audioDuration = 0;
        this.currentTime = 0;

        // Intervals
        this.durationInterval = null;
        this.playInterval = null;

        this.initialized = false;

        this.length = 30;
    };

    LHCVoiceVisitorMessage.prototype.setStateElement = function(element, state) {
        if (state === true) {
            $('#voice-control-message .' + element).show();
        } else {
            $('#voice-control-message .' + element).hide();
        }
    }

    LHCVoiceVisitorMessage.prototype.updateUIByState = function() {
        this.setStateElement('voice-start-recording',this.isRecording === false);
        this.setStateElement('voice-stop-recording',this.isRecording === true);
        this.setStateElement('voice-play-recording',this.recording !== null && this.isPlaying === false);
        this.setStateElement('voice-stop-play',this.recording !== null && this.isPlaying === true);
        this.setStateElement('voice-send-message',this.recording !== null);

        if (this.isRecording === true || (this.recording !== null && this.isPlaying === false)) {
            $('.voice-audio-status').text((this.isRecording === true ? (this.length - this.audioDuration) : this.audioDuration) + 's.');
        } else if (this.isPlaying === true) {
            $('.voice-audio-status').text(this.currentTime + 's.');
        } else {
            $('.voice-audio-status').text('0s.');
        }
    }

    LHCVoiceVisitorMessage.prototype.startRecording = async function() {

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

            this.durationInterval = setInterval(() => {
                this.audioDuration++;

                if (this.audioDuration >= this.length) {
                    this.stopRecording();
                }

                this.updateUIByState();
            }, 1000);

            this.updateUIByState();

        } catch (e) {
            console.log(e);
            alert('Sorry but voice messages are not supported on your browser!');
        }
    }

    LHCVoiceVisitorMessage.prototype.stopRecording = async function() {
        const blob = await recorder.stopRecording();

        this.recording = blob;
        this.audio = new Audio();
        this.audio.src = URL.createObjectURL(blob);

        this.isRecording = false;

        clearInterval(this.durationInterval);

        this.updateUIByState();
    }

    LHCVoiceVisitorMessage.prototype.playRecord = function() {

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

    LHCVoiceVisitorMessage.prototype.stopPlayRecord = function() {

        if (this.isPlaying === true) {
            clearInterval(this.playInterval);
            this.audio.currentTime = 0;
            this.audio.pause();
            this.isPlaying = false;
        }

        this.updateUIByState();
    }

    LHCVoiceVisitorMessage.prototype.prepareUIForRecording = function() {

        this.recording = null;
        this.isRecording = false;
        this.isPlaying = false;
        this.isLoading = false;
        this.audioDuration = 0;
        this.currentTime = 0;

        $('#lhc-mic-icon').hide();
        $('#voice-control-message').show();

        this.updateUIByState();

        var _this = this;

        if (this.initialized === false) {

            this.initialized = true;

            $('#voice-control-message .leave-recording-ui').click(function(e){
                _this.leaveVoiceUI();
                e.preventDefault();
                e.stopPropagation();
            });

            $('#voice-control-message .voice-start-recording').click(function(e){
                _this.startRecording();
                e.preventDefault();
                e.stopPropagation();
            });

            $('#voice-control-message .voice-stop-recording').click(function(e){
                _this.stopRecording();
                e.preventDefault();
                e.stopPropagation();
            });

            $('#voice-control-message .voice-play-recording').click(function(e){
                _this.playRecord();
                e.preventDefault();
                e.stopPropagation();
            });

            $('#voice-control-message .voice-stop-play').click(function(e){
                _this.stopPlayRecord();
                e.preventDefault();
                e.stopPropagation();
            });

            $('#voice-control-message .voice-send-message').click(function(e){
                _this.sendVoiceMessage();
                e.preventDefault();
                e.stopPropagation();
            });
        }
    }

    LHCVoiceVisitorMessage.prototype.sendVoiceMessage = function() {
        const req = new XMLHttpRequest();
        const formData = new FormData();
        formData.append("files", this.recording, "record.mp3");
        req.open("POST", WWW_DIR_JAVASCRIPT + '/file/uploadfile/' + this.chat_id + '/' + this.hash);
        req.upload.addEventListener("load", event => {
            this.leaveVoiceUI();
            lhinst.syncusercall();
        });
        req.send(formData);
    }

    LHCVoiceVisitorMessage.prototype.leaveVoiceUI = function() {
        $('#lhc-mic-icon').show();
        $('#voice-control-message').hide();

        if (this.isRecording) {
            recorder.stopRecording();
        }

        this.stopPlayRecord();

        // Just free up memory
        this.recording = null;
        this.audio = null;
    }

    LHCVoiceVisitorMessage.prototype.listen = function(params) {

        this.chat_id = params['chat_id'];
        this.hash = params['hash'];
        this.length = params['length'];

        this.prepareUIForRecording();
    };

    return new LHCVoiceVisitorMessage();
})();
