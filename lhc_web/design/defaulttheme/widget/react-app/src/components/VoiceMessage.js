import React, { PureComponent } from 'react';
import vmsg from "vmsg";
import { withTranslation } from 'react-i18next';

const recorder = new vmsg.Recorder({
    wasmURL: window.lhcChat['staticJS']['chunk_js']+ "/" + 'vmsg.8c4a15f2.wasm',
    shimURL: "https://unpkg.com/wasm-polyfill.js@0.2.0/wasm-polyfill.js"
});

class VoiceMessage extends PureComponent {

    state = {
        isLoading: false,
        isRecording: false,
        isPlaying: false,
        recording: null,
        audioDuration: 0,
        currentTime: 0,
        isTranscribing: false
    };

    constructor(props) {
        super(props);
        this.startRecording = this.startRecording.bind(this);
        this.stopRecording = this.stopRecording.bind(this);
        this.playRecord = this.playRecord.bind(this);
        this.stopPlayRecord = this.stopPlayRecord.bind(this);
        this.sendRecord = this.sendRecord.bind(this);

        this.startWebkitRecording = this.startWebkitRecording.bind(this);
        this.stopWebkitRecording = this.stopWebkitRecording.bind(this);
        this.toggleWebkitRecording = this.toggleWebkitRecording.bind(this);

        // Intervals
        this.durationInterval = null;
        this.playInterval = null;
        this.recognition = null;
    }

    async startRecording() {
        this.stopPlayRecord();
        this.setState({ isLoading: true, audioDuration : 0, recording: null, isPlaying: false, currentTime : 0});
        try {
            await recorder.initAudio();
            await recorder.initWorker();
            recorder.startRecording();
            this.setState({ isLoading: false, isRecording: true });
            this.durationInterval = setInterval(() => {
                this.setState({ audioDuration: this.state.audioDuration + 1 });

                // Do not allow to record longer message than defined messages length.
                if (this.state.audioDuration >= this.props.maxSeconds) {
                    this.stopRecording();
                }

            }, 1000);
        } catch (e) {
            alert('Sorry but voice messages are not supported on your browser!');
            this.setState({ isLoading: false });
        }
    }

    async stopRecording(){
        const blob = await recorder.stopRecording();

        this.audio = new Audio();
        this.audio.src = URL.createObjectURL(blob);

        this.setState({
            isLoading: false,
            isRecording: false,
            recording: blob
        });

        clearInterval(this.durationInterval);
    }

    playRecord() {
        this.audio.currentTime = 0;
        this.audio.play();
        this.setState({isPlaying : true});

        this.playInterval = setInterval(
            () => {
                this.setState({currentTime: Math.round(this.audio.currentTime)});
                if (this.audio.ended || this.audio.paused) {
                    this.stopPlayRecord();
                }
            },
        1000);
    }

    stopPlayRecord() {
        if (this.state.isPlaying) {
            clearInterval(this.playInterval);
            this.audio.currentTime = 0;
            this.audio.pause();
            this.setState({isPlaying : false, currentTime: 0});
        }
    }

    sendRecord() {
        const { t } = this.props;

        this.props.progress(t('file.uploading'));

        const req = new XMLHttpRequest();
        const formData = new FormData();
        formData.append("files", this.state.recording, "record.mp3");
        req.open("POST", this.props.base_url + '/file/uploadfile/' + this.props.chat_id + '/' + this.props.hash);
        req.upload.addEventListener("load", event => {
            this.props.progress('100%');
            this.props.onCompletion();
            this.props.cancel();
        });
        req.send(formData);
    }

    startWebkitRecording() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            alert('Speech recognition is not supported in your browser!');
            this.props.cancel();
            return;
        }

        this.recognition = new SpeechRecognition();
        this.recognition.continuous = true;
        this.recognition.interimResults = true;
        if (this.props.lang) {
            this.recognition.lang = this.props.lang;
        }

        this.recognition.onresult = (event) => {
            let transcript = '';
            for (let i = 0; i < event.results.length; i++) {
                transcript += event.results[i][0].transcript;
            }
            this.props.setText(transcript);
        };

        this.recognition.onerror = () => {
            this.recognition = null;
            this.setState({ isTranscribing: false });
            this.props.cancel();
        };

        this.recognition.onend = () => {
            this.recognition = null;
            this.setState({ isTranscribing: false });
            this.props.cancel();
        };

        this.recognition.start();
        this.setState({ isTranscribing: true });
    }

    stopWebkitRecording() {
        if (this.recognition) {
            this.recognition.onend = null;
            this.recognition.onerror = null;
            this.recognition.stop();
            this.recognition = null;
        }
        this.setState({ isTranscribing: false });
        this.props.cancel();
    }

    toggleWebkitRecording() {
        if (this.state.isTranscribing) {
            this.stopWebkitRecording();
        } else {
            this.startWebkitRecording();
        }
    }

    componentDidMount() {
        if (this.props.voice_engine == 1) {
            this.startWebkitRecording();
        }
    }

    componentWillUnmount() {

        // Stop playing if anything is playing
        this.stopPlayRecord();

        // Stop recording if it's recording
        if (this.state.isRecording) {
            recorder.stopRecording();
        }

        // Stop webkit recognition if active
        if (this.recognition) {
            this.recognition.onend = null;
            this.recognition.onerror = null;
            this.recognition.stop();
            this.recognition = null;
        }
    }

    pad(n) {
        return (n < 10) ? ("0" + n) : n;
    }

    render() {

        const {isLoading, isRecording, recording, isPlaying, isTranscribing } = this.state;

        const { t } = this.props;

        if (this.props.voice_engine == 1) {
            return <div className="text-nowrap voice-message-container">
                <button type="button" tabIndex="0" className={"material-icons material-icons-button fs25 pointer me-0 " + (isTranscribing ? 'text-danger' : 'text-muted')} title={isTranscribing ? t('voice.stop_recording') : t('voice.record_voice_message')} onClick={this.toggleWebkitRecording}>&#xf10b;</button>
            </div>;
        }

        return <div className="text-nowrap voice-message-container">
            <button type="button" tabIndex="0" className="material-icons material-icons-button pointer text-danger fs25" title={t('voice.cancel_voice_message')} onClick={() => this.props.cancel()}>&#xf10a;</button>

            {!isRecording && <button type="button" tabIndex="0" className="material-icons material-icons-button fs25 pointer text-danger me-0" title={t('voice.record_voice_message')} onClick={this.startRecording}>&#xf10f;</button>}

            {isRecording && <button type="button" tabIndex="0" className="material-icons material-icons-button fs25 pointer text-danger me-0" title={t('voice.stop_recording')} onClick={this.stopRecording}>&#xf112;</button>}

            {recording && isPlaying === false && <button type="button" tabIndex="0" className="material-icons material-icons-button pointer text-success me-0 fs25" title={t('voice.play_recorded')} onClick={this.playRecord}>&#xf111;</button>}

            {recording && isPlaying === true && <button type="button" tabIndex="0" className="material-icons material-icons-button pointer text-success me-0 fs25" title={t('voice.stop_playing_recorded')} onClick={this.stopPlayRecord}>&#xf112;</button>}

            <span className="fs12 px-1 voice-message-length">{isRecording ? '' : (isPlaying ? this.pad(this.state.currentTime) + ':' : '')}{isRecording || !recording ? (this.props.maxSeconds - this.state.audioDuration) + " s." : this.pad(this.state.audioDuration) + (!isPlaying ? 's.' : '')}</span>

            {recording && <button type="button" tabIndex="0" className="material-icons material-icons-button pointer text-success me-0 fs25" title={t('voice.send')} onClick={this.sendRecord}>&#xf107;</button>}

        </div>;
    }
}

export default withTranslation()(VoiceMessage);