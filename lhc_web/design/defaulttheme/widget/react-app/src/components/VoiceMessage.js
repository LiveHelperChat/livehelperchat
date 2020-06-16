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
        currentTime: 0
    };

    constructor(props) {
        super(props);
        this.startRecording = this.startRecording.bind(this);
        this.stopRecording = this.stopRecording.bind(this);
        this.playRecord = this.playRecord.bind(this);
        this.stopPlayRecord = this.stopPlayRecord.bind(this);
        this.sendRecord = this.sendRecord.bind(this);

        // Intervals
        this.durationInterval = null;
        this.playInterval = null;
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

    componentDidMount() {

    }

    componentWillUnmount() {

        // Stop playing if anything is playing
        this.stopPlayRecord();

        // Stop recordng if it's recording
        if (this.state.isRecording) {
            recorder.stopRecording();
        }
    }

    pad(n) {
        return (n < 10) ? ("0" + n) : n;
    }

    render() {

        const {isLoading, isRecording, recording, isPlaying } = this.state;

        const { t } = this.props;

        return <div className="text-nowrap">
            <i className="material-icons pointer text-danger fs25" title={t('voice.cancel_voice_message')} onClick={() => this.props.cancel()}>&#xf10a;</i>

            {!isRecording && <i className="material-icons fs25 pointer text-danger mr-0" title={t('voice.record_voice_message')} onClick={this.startRecording}>&#xf10f;</i>}

            {isRecording && <i className="material-icons fs25 pointer text-danger mr-0" title={t('voice.stop_recording')} onClick={this.stopRecording}>&#xf112;</i>}

            {recording && isPlaying === false && <i className="material-icons pointer text-success mr-0 fs25" title={t('voice.play_recorded')} onClick={this.playRecord}>&#xf111;</i>}

            {recording && isPlaying === true && <i className="material-icons pointer text-success mr-0 fs25" title={t('voice.stop_playing_recorded')} onClick={this.stopPlayRecord}>&#xf112;</i>}

            <span className="fs12 px-1">{isRecording ? '' : (isPlaying ? this.pad(this.state.currentTime) + ':' : '')}{isRecording || !recording ? (this.props.maxSeconds - this.state.audioDuration) + " s." : this.pad(this.state.audioDuration) + (!isPlaying ? 's.' : '')}</span>

            {recording && <i className="material-icons pointer text-success mr-0 fs25" title={t('voice.send')} onClick={this.sendRecord}>&#xf107;</i>}

        </div>;
    }
}

export default withTranslation()(VoiceMessage);