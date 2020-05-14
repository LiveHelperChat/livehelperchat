//https://medium.com/@MilkMan/read-this-before-refactoring-your-big-react-class-components-to-hooks-515437e9d96f
//https://reactjs.org/docs/hooks-reference.html#usereducer
import React, { useEffect, useState, useReducer } from "react";
import axios from "axios";

const GroupChat = props => {
    const [data, setData] = useState([]);
    const [isLoaded, setLoaded] = useState(false);
    const [ignored, forceUpdate] = useReducer(x => x + 1, 0);
    const [isCollapsed, setCollapsed] = useState(false);

    useEffect(() => {
        console.log(props.chatId);
    },[]);

    return (
        <React.Fragment>
            <div className="row">
                {props.chatId}
            </div>
        </React.Fragment>
    );
}

export default GroupChat