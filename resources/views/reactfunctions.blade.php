<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel Cache Locks & React</title>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://unpkg.com/react@17/umd/react.development.js" crossorigin></script>
        <script src="https://unpkg.com/react-dom@17/umd/react-dom.development.js" crossorigin></script>
        <script src="https://unpkg.com/babel-standalone@6/babel.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.24.0/axios.min.js" integrity="sha512-u9akINsQsAkG9xjc1cnGF4zw5TFDwkxuc9vUp5dltDWYCSmyd0meygbvgXrlc/z7/o4a19Fb5V0OUE58J7dcyw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
        <script type="text/babel">
        'use strict';
        const { useState, useEffect, useRef } = React;

        function Board() {
            const attempts = 20;

            const [holding, setHolding] = useState(null);
            const [completed, setCompleted] = useState(0);
            const [showSummary, setShowSummary] = useState(false);

            const processCompleted = function(playerName) {
                const nowCompleted = completed + 1;
                setCompleted(nowCompleted);

                if (nowCompleted === 6) {
                    setShowSummary(true);
                }
            }

            return (
                <div className="grid grid-cols-3 gap-3">
                    <Player
                        name="A"
                        attempts={attempts}
                        showSummary={showSummary}
                        holding={holding} haveLock={setHolding}
                        completed={processCompleted}
                        colour="green">
                    </Player>
                    <Player
                        name="B"
                        attempts={attempts}
                        showSummary={showSummary}
                        holding={holding} haveLock={setHolding}
                        completed={processCompleted}
                        colour="blue">
                    </Player>
                    <Player
                        name="C"
                        attempts={attempts}
                        showSummary={showSummary}
                        holding={holding} haveLock={setHolding}
                        completed={processCompleted}
                        colour="red">
                    </Player>
                    <Player
                        name="D"
                        attempts={attempts}
                        showSummary={showSummary}
                        holding={holding} haveLock={setHolding}
                        completed={processCompleted}
                        colour="purple">
                    </Player>
                    <Player
                        name="E"
                        attempts={attempts}
                        showSummary={showSummary}
                        holding={holding} haveLock={setHolding}
                        completed={processCompleted}
                        colour="yellow">
                    </Player>
                    <Player
                        name="F"
                        attempts={attempts}
                        showSummary={showSummary}
                        holding={holding} haveLock={setHolding}
                        completed={processCompleted}
                        colour="orange">
                    </Player>
                </div>
            );
        }

        function Player(props) {
            const [time, setTime] = useState(Date.now());
            const [counter, setCounter] = useState(0);
            const [locks, setLocks] = useState(0);
            const timer = useRef();

            const getRandomTimerSeconds = function() {
                return (Math.floor(Math.random() * 30) + 1) * 100;
            }

            useEffect(() => {
                timer.current = setTimeout(() => { acquireTheLock() }, getRandomTimerSeconds());
                return () => clearTimeout(timer.current);
            }, [time]);

            useEffect(() => {
                if (counter < props.attempts) {
                    return;
                }

                clearTimeout(timer.current);
                timer.current = null;
                props.completed(props.name);

            }, [counter]);

            const acquireTheLock = function() {
                setTime(Date.now());
                setCounter((counter) => counter + 1);

                axios.get(`http://lock.test/api/attempt`)
                    .then(res => {
                        if (res.data.hasLock === true) {
                            setLocks((locks) => locks + 1);
                            props.haveLock(props.name);
                        }
                    })
            }

            const shouldShowSummary = props.showSummary;
            const currentlyHolding = (props.holding == props.name);
            const textColour = 'text-3xl font-black text-'+props.colour+'-600';
            const divStyle = {
                minHeight: 400+'px',
            };

            return (
                <div className="text-center" style={divStyle}>
                    <h2 className={textColour}>Player: {props.name}</h2>
                    <p className="text-gray-500">locks acquired: {locks}</p>
                    <p className="text-gray-700">attempts: {counter}</p>
                    {currentlyHolding && shouldShowSummary == false && <i className="mt-5 fas fa-lock text-yellow-400 fa-4x"></i>}
                    {shouldShowSummary && <h4 className="text-5xl font-black">{Math.round((locks/counter)*100)}%</h4>}
                </div>
            );
        }

        ReactDOM.render(
            React.createElement(Board),
            document.querySelector('#board')
        );
    </script>
    </head>
    <body class="antialiased">
        <div class="container mx-auto align-middle">
            <h1 class="text-3xl font-black text-center my-5">Laravel React - Cache Lock Challenge</h1>
            <h4 class="text-xl font-black text-gray-600 text-center mb-10">The players have <i>n</i> attempts to try to acquire the atomic cache lock from the api</h4>
            <div id="board"></div>
        </div>
    </body>
</html>
