<svg viewBox="-2 -2 105 105" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <filter id="f1" x="0" y="0" width="200%" height="200%">
            <feOffset result="offOut" in="SourceAlpha" dx="2" dy="2" />
            <feGaussianBlur result="blurOut" in="offOut" stdDeviation="1" />
            <feBlend in="SourceGraphic" in2="blurOut" mode="normal" />
        </filter>
    </defs>
    <circle cx="50" cy="50" r="49" stroke="{{lhc.chatMetaData[chat.id]['um'] == 1 ? '#BB474A' : '#5da423'}}" stroke-width="3" fill="{{lhc.chatMetaData[chat.id]['cbg']}}" />
    <circle filter="url(#f1)" cx="70" cy="75" r="12" fill="{{lhc.chatMetaData[chat.id]['ucs'] == 0 ? '#5da423' : (lhc.chatMetaData[chat.id]['ucs'] == 2 ? '#EDC21A' : '#BB474A')}}" />
    <text x="48%" y="48%" text-anchor="middle" stroke="white" stroke-width="2px" style="text-shadow: 1px 1px 2px black;font-size:26px;" dy=".3em">{{lhc.chatMetaData[chat.id]['ctit']}} {{lhc.chatMetaData[chat.id]['mn'] > 0 ? '(' + lhc.chatMetaData[chat.id]['mn'] + ')' : ''}}</text>
</svg>