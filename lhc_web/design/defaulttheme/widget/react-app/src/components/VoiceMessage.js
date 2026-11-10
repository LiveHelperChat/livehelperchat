import React, { PureComponent } from 'react';
import vmsg from "vmsg";
import { withTranslation } from 'react-i18next';

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
        // A reopened panel must not share a pending microphone request with the old one.
        this.recorder = new vmsg.Recorder({
            wasmURL: window.lhcChat['staticJS']['chunk_js']+ "/" + 'vmsg.8c4a15f2.wasm',
            shimURL: "https://unpkg.com/wasm-polyfill.js@0.2.0/wasm-polyfill.js"
        });
        this.isUnmounted = false;
        this.recordingStarting = false;
        this.startRecording = this.startRecording.bind(this);
        this.stopRecording = this.stopRecording.bind(this);
        this.playRecord = this.playRecord.bind(this);
        this.stopPlayRecord = this.stopPlayRecord.bind(this);
        this.sendRecord = this.sendRecord.bind(this);

        this.startWebkitRecording = this.startWebkitRecording.bind(this);
        this.stopWebkitRecording = this.stopWebkitRecording.bind(this);
        this.toggleWebkitRecording = this.toggleWebkitRecording.bind(this);

        // Live input level while recording (bars in .voice-message-level)
        this.levelRef = React.createRef();
        this.levelAnalyser = null;
        this.levelFrame = null;
        this.levelHistory = [];

        // Intervals
        this.durationInterval = null;
        this.playInterval = null;
        this.recognition = null;
        this.recognitionParts = [];
        this.androidTranscript = '';
        this.lastTranscript = '';
    }

    normalizeSpeechText(text) {
        return (text || '').replace(/\s+/g, ' ').trim();
    }

    mergeSpeechText(previous, incoming) {
        const prev = this.normalizeSpeechText(previous);
        const next = this.normalizeSpeechText(incoming);

        if (prev === '') return next;
        if (next === '') return prev;
        if (next.startsWith(prev)) return next;
        if (prev.startsWith(next)) return prev;
        if (next.includes(prev)) return next;
        if (prev.includes(next)) return prev;

        const prevLower = prev.toLowerCase();
        const nextLower = next.toLowerCase();

        const maxOverlap = Math.min(prevLower.length, nextLower.length);
        for (let overlap = maxOverlap; overlap > 0; overlap--) {
            if (prevLower.slice(prevLower.length - overlap) === nextLower.slice(0, overlap)) {
                return (prev + ' ' + next.slice(overlap)).replace(/\s+/g, ' ').trim();
            }
        }

        return (prev + ' ' + next).replace(/\s+/g, ' ').trim();
    }

    setParentStatus(text) {
        if (typeof this.props.progress === 'function') {
            this.props.progress(text);
        }
    }

    async startRecording() {
        if (this.isUnmounted || this.recordingStarting || this.state.isRecording) {
            return;
        }
        this.recordingStarting = true;
        this.stopPlayRecord();
        this.setState({ isLoading: true, audioDuration : 0, recording: null, isPlaying: false, currentTime : 0});
        try {
            await this.recorder.initAudio();
            if (this.isUnmounted) {
                this.closeRecorder();
                return;
            }
            await this.recorder.initWorker();
            if (this.isUnmounted) {
                this.closeRecorder();
                return;
            }
            this.recorder.startRecording();
            this.setState({ isLoading: false, isRecording: true }, () => this.startLevelMeter());
            this.durationInterval = setInterval(() => {
                this.setState({ audioDuration: this.state.audioDuration + 1 });

                // Do not allow to record longer message than defined messages length.
                if (this.state.audioDuration >= this.props.maxSeconds) {
                    this.stopRecording();
                }

            }, 1000);
        } catch (e) {
            this.closeRecorder();
            if (this.isUnmounted) {
                return;
            }
            alert('Sorry but voice messages are not supported on your browser!');
            this.setState({ isLoading: false });
        } finally {
            this.recordingStarting = false;
        }
    }

    closeRecorder() {
        // stopRecording() may already have closed the AudioContext.
        if (this.recorder.audioCtx && this.recorder.audioCtx.state === 'closed') {
            this.recorder.audioCtx = null;
        }
        this.recorder.close();
        this.recorder.audioCtx = null;
    }

    startLevelMeter() {
        this.stopLevelMeter();
        try {
            if (!this.recorder.audioCtx || !this.recorder.gainNode || typeof this.recorder.audioCtx.createAnalyser !== 'function') {
                return;
            }
            const analyser = this.recorder.audioCtx.createAnalyser();
            analyser.fftSize = 512;
            this.recorder.gainNode.connect(analyser);
            this.levelAnalyser = analyser;
        } catch (e) {
            return;
        }

        const samples = new Uint8Array(this.levelAnalyser.fftSize);
        let lastSample = 0;

        const draw = (time) => {
            if (this.levelAnalyser === null) {
                return;
            }
            // A new bar every 80ms, like a waveform moving to the left
            if (time - lastSample >= 80) {
                lastSample = time;
                this.levelAnalyser.getByteTimeDomainData(samples);
                let sum = 0;
                for (let i = 0; i < samples.length; i++) {
                    const v = (samples[i] - 128) / 128;
                    sum += v * v;
                }
                const level = Math.min(1, Math.sqrt(sum / samples.length) * 4);
                const bars = this.levelRef.current ? this.levelRef.current.children : [];
                this.levelHistory.push(level);
                if (this.levelHistory.length > bars.length) {
                    this.levelHistory.splice(0, this.levelHistory.length - bars.length);
                }
                const offset = bars.length - this.levelHistory.length;
                for (let i = 0; i < bars.length; i++) {
                    const value = i >= offset ? this.levelHistory[i - offset] : 0;
                    bars[i].style.transform = 'scaleY(' + Math.max(0.12, value).toFixed(3) + ')';
                }
            }
            this.levelFrame = window.requestAnimationFrame(draw);
        };

        this.levelFrame = window.requestAnimationFrame(draw);
    }

    stopLevelMeter() {
        if (this.levelFrame !== null) {
            window.cancelAnimationFrame(this.levelFrame);
            this.levelFrame = null;
        }
        if (this.levelAnalyser !== null) {
            try { this.recorder.gainNode.disconnect(this.levelAnalyser); } catch (e) {}
            try { this.levelAnalyser.disconnect(); } catch (e) {}
            this.levelAnalyser = null;
        }
        this.levelHistory = [];
    }

    async stopRecording(){
        clearInterval(this.durationInterval);
        this.stopLevelMeter();
        const blob = await this.recorder.stopRecording();
        if (this.isUnmounted) {
            return;
        }

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
        // console.log('[VoiceMessage] startWebkitRecording()', {
        //     hasSpeechRecognition: !!window.SpeechRecognition,
        //     hasWebkitSpeechRecognition: !!window.webkitSpeechRecognition,
        //     userAgent: navigator.userAgent,
        //     lang: this.props.lang,
        //     voiceEngine: this.props.voice_engine
        // });

        const isAndroid = /android/i.test(navigator.userAgent || '');

        this.recognition = new SpeechRecognition();
        this.recognition.continuous = true;
        this.recognition.interimResults = true;
        this.recognition.maxAlternatives = 1;
        if (this.props.lang) {
            this.recognition.lang = this.props.lang;
        }

        // console.log('[VoiceMessage] recognition configured', {
        //     continuous: this.recognition.continuous,
        //     interimResults: this.recognition.interimResults,
        //     maxAlternatives: this.recognition.maxAlternatives,
        //     lang: this.recognition.lang,
        //     isAndroid: isAndroid
        // });

        this.recognitionParts = [];
        this.androidTranscript = '';
        this.lastTranscript = '';

        this.recognition.onstart = () => {
            // console.log('[VoiceMessage] recognition onstart');
        };

        this.recognition.onaudiostart = () => {
            // console.log('[VoiceMessage] recognition onaudiostart');
        };

        this.recognition.onsoundstart = () => {
            // console.log('[VoiceMessage] recognition onsoundstart');
        };

        this.recognition.onspeechstart = () => {
            // console.log('[VoiceMessage] recognition onspeechstart');
        };

        this.recognition.onresult = (event) => {
            for (let i = event.resultIndex; i < event.results.length; i++) {
                this.recognitionParts[i] = {
                    text: (event.results[i][0].transcript || '').replace(/\s+/g, ' ').trim(),
                    isFinal: event.results[i].isFinal
                };
            }

            const parts = this.recognitionParts
                .slice(0, event.results.length)
                .map(part => part && part.text ? part.text : '')
                .filter(Boolean);

            let transcript = parts
                .join(' ')
                .replace(/\s+/g, ' ')
                .trim();

            // Android engines often duplicate by reporting cumulative text across slots.
            if (isAndroid) {
                const longestPart = parts.reduce((longest, current) => {
                    return current.length > longest.length ? current : longest;
                }, '');

                const lastPart = parts.length > 0 ? parts[parts.length - 1] : '';
                const candidate = longestPart.length >= lastPart.length ? longestPart : lastPart;

                this.androidTranscript = this.mergeSpeechText(this.androidTranscript, candidate);
                transcript = this.androidTranscript;
            }

            const finalCount = this.recognitionParts
                .slice(0, event.results.length)
                .reduce((acc, part) => acc + (part && part.isFinal ? 1 : 0), 0);

            // console.log('[VoiceMessage] recognition onresult', {
            //     resultIndex: event.resultIndex,
            //     resultsLength: event.results.length,
            //     finalCount: finalCount,
            //     transcriptLength: transcript.length,
            //     transcriptPreview: transcript.substring(0, 120)
            // });

            if (transcript !== this.lastTranscript) {
                this.lastTranscript = transcript;
                this.props.setText(transcript);
            } else {
                // console.log('[VoiceMessage] transcript unchanged, skipping setText');
            }
        };

        this.recognition.onerror = (event) => {
            console.error('[VoiceMessage] recognition onerror', {
                error: event && event.error,
                message: event && event.message,
                type: event && event.type
            });
            this.setParentStatus('Voice recognition error: ' + (event && event.error ? event.error : 'unknown'));
            this.recognition = null;
            this.setState({ isTranscribing: false });
            this.props.cancel();
          };

        this.recognition.onspeechend = () => {
            // console.log('[VoiceMessage] recognition onspeechend');
        };

        this.recognition.onsoundend = () => {
            // console.log('[VoiceMessage] recognition onsoundend');
        };

        this.recognition.onaudioend = () => {
            // console.log('[VoiceMessage] recognition onaudioend');
        };

        this.recognition.onend = () => {
            // console.log('[VoiceMessage] recognition onend');
            // this.setParentStatus('Voice recognition stopped');
            this.recognition = null;
            this.setState({ isTranscribing: false });
            this.props.cancel();
        };

        try {
            this.recognition.start();
            // console.log('[VoiceMessage] recognition.start() called');
            // this.setParentStatus('Voice recognition active');
        } catch (err) {
            console.error('[VoiceMessage] recognition.start() failed', err);
            this.setParentStatus('Voice recognition failed to start');
            this.recognition = null;
            this.setState({ isTranscribing: false });
            this.props.cancel();
            return;
        }

        this.setState({ isTranscribing: true });
    }

    stopWebkitRecording() {
        // console.log('[VoiceMessage] stopWebkitRecording()', {
        //     hasRecognition: !!this.recognition,
        //     isTranscribing: this.state.isTranscribing
        // });
        if (this.recognition) {
            this.recognition.onend = null;
            this.recognition.onerror = null;
            this.recognition.onstart = null;
            this.recognition.onspeechstart = null;
            this.recognition.onspeechend = null;
            this.recognition.onsoundstart = null;
            this.recognition.onsoundend = null;
            this.recognition.onaudiostart = null;
            this.recognition.onaudioend = null;
            this.recognition.stop();
            this.recognition = null;
        }
        this.recognitionParts = [];
        this.androidTranscript = '';
        this.lastTranscript = '';
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
        } else {
            // The visitor already pressed "Record voice message" - start right away
            this.startRecording();
        }
    }

    componentWillUnmount() {
        this.isUnmounted = true;
        clearInterval(this.durationInterval);
        clearInterval(this.playInterval);

        // console.log('[VoiceMessage] componentWillUnmount()', {
        //     isRecording: this.state.isRecording,
        //     hasRecognition: !!this.recognition
        // });

        // Stop playing if anything is playing
        this.stopPlayRecord();
        this.stopLevelMeter();

        // Release microphone/worker even if initialization has not finished yet.
        this.closeRecorder();

        // Stop webkit recognition if active
        if (this.recognition) {
            this.recognition.onend = null;
            this.recognition.onerror = null;
            this.recognition.onstart = null;
            this.recognition.onspeechstart = null;
            this.recognition.onspeechend = null;
            this.recognition.onsoundstart = null;
            this.recognition.onsoundend = null;
            this.recognition.onaudiostart = null;
            this.recognition.onaudioend = null;
            this.recognition.stop();
            this.recognition = null;
        }

        this.recognitionParts = [];
        this.androidTranscript = '';
        this.lastTranscript = '';
    }

    pad(n) {
        return (n < 10) ? ("0" + n) : n;
    }

    render() {

        const {isLoading, isRecording, recording, isPlaying, isTranscribing } = this.state;

        const { t } = this.props;

        if (this.props.voice_engine == 1) {
            return <div className="text-nowrap voice-message-container">
                <button type="button" tabIndex="0" className={"material-icons material-icons-button fs25 pointer me-0 " + (isTranscribing ? 'text-danger message-row-typing' : 'text-muted')} title={isTranscribing ? t('voice.stop_dictate') : t('voice.dictate')} onClick={this.toggleWebkitRecording}>&#xf10b;</button>
            </div>;
        }

        return <div className="text-nowrap voice-message-container">
            <button type="button" tabIndex="0" className="material-icons material-icons-button pointer text-danger fs25" title={t('voice.cancel_voice_message')} onClick={() => this.props.cancel()}>&#xf10a;</button>

            {!isRecording && <button type="button" tabIndex="0" className="material-icons material-icons-button fs25 pointer text-danger me-0" title={t('voice.record_voice_message')} onClick={this.startRecording}>&#xf10f;</button>}

            {isRecording && <button type="button" tabIndex="0" className="material-icons material-icons-button fs25 pointer text-danger me-0" title={t('voice.stop_recording')} onClick={this.stopRecording}>&#xf112;</button>}

            {recording && isPlaying === false && <button type="button" tabIndex="0" className="material-icons material-icons-button pointer text-success me-0 fs25" title={t('voice.play_recorded')} onClick={this.playRecord}>&#xf111;</button>}

            {recording && isPlaying === true && <button type="button" tabIndex="0" className="material-icons material-icons-button pointer text-success me-0 fs25" title={t('voice.stop_playing_recorded')} onClick={this.stopPlayRecord}>&#xf112;</button>}

            {isRecording && <span className="voice-message-level" ref={this.levelRef} aria-hidden="true">{Array.from({length: 24}, (v, i) => <i key={i}></i>)}</span>}

            <span className="fs12 px-1 voice-message-length">{isRecording ? '' : (isPlaying ? this.pad(this.state.currentTime) + ':' : '')}{isRecording || !recording ? (this.props.maxSeconds - this.state.audioDuration) + " s." : this.pad(this.state.audioDuration) + (!isPlaying ? 's.' : '')}</span>

            {recording && <button type="button" tabIndex="0" className="material-icons material-icons-button pointer text-success me-0 fs25" title={t('voice.send')} onClick={this.sendRecord}>&#xf107;</button>}

        </div>;
    }
}

export default withTranslation()(VoiceMessage);
