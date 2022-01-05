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
        class Board extends React.Component {
            constructor(props) {
                super(props);
                this.state = {holding: null, attempts: 10, completed: 0, showSummary: false};
            }

            haveLock(playerName) {
                this.setState({holding: playerName});
            }

            completed(playerName) {
                const completed = this.state.completed;
                this.setState({completed: completed + 1});

                if (this.state.completed == 6) {
                    this.setState({showSummary: true})
                }
            }

            render() {
                return (
                    <div className="grid grid-cols-3 gap-3">
                        <Player
                            name="A"
                            attempts={this.state.attempts}
                            showSummary={this.state.showSummary}
                            holding={this.state.holding}
                            haveLock={this.haveLock.bind(this)}
                            completed={this.completed.bind(this)}
                            colour="green">
                        </Player>
                        <Player
                            name="B"
                            attempts={this.state.attempts}
                            showSummary={this.state.showSummary}
                            holding={this.state.holding}
                            haveLock={this.haveLock.bind(this)}
                            completed={this.completed.bind(this)}
                            colour="blue">
                        </Player>
                        <Player
                            name="C"
                            attempts={this.state.attempts}
                            showSummary={this.state.showSummary}
                            holding={this.state.holding}
                            haveLock={this.haveLock.bind(this)}
                            completed={this.completed.bind(this)}
                            colour="red">
                        </Player>
                        <Player
                            name="D"
                            attempts={this.state.attempts}
                            showSummary={this.state.showSummary}
                            holding={this.state.holding}
                            haveLock={this.haveLock.bind(this)}
                            completed={this.completed.bind(this)}
                            colour="purple">
                        </Player>
                        <Player
                            name="E"
                            attempts={this.state.attempts}
                            showSummary={this.state.showSummary}
                            holding={this.state.holding}
                            haveLock={this.haveLock.bind(this)}
                            completed={this.completed.bind(this)}
                            colour="yellow">
                        </Player>
                        <Player
                            name="F"
                            attempts={this.state.attempts}
                            showSummary={this.state.showSummary}
                            holding={this.state.holding}
                            haveLock={this.haveLock.bind(this)}
                            completed={this.completed.bind(this)}
                            colour="orange">
                        </Player>
                    </div>
                );
            }
        }

        class Player extends React.Component {

            constructor(props) {
                super(props);
                this.state = {
                    time: Date.now(),
                    timeout: null,
                    counter: 0,
                    locks: 0
                };
            }

            componentDidMount() {
                this.setState({timeout: setTimeout(() => this.acquireTheLock(), 1000)});
            }

            componentWillUnmount() {
                clearTimeout(this.state.timeout);
            }

            acquireTheLock() {
                const counter = this.state.counter + 1;

                this.setState({ time: Date.now(), counter: counter });

                axios.get(`http://lock.test/api/attempt`)
                .then(res => {
                    if (res.data.hasLock === true) {
                        const locks = this.state.locks + 1;
                        this.setState({ locks: locks });
                        this.props.haveLock(this.props.name);
                    }
                })

                if (counter >= this.props.attempts) {
                    clearTimeout(this.state.timeout);
                    this.props.completed(this.props.name);

                    return;
                }

                const retryInSeconds = (Math.floor(Math.random() * 30) + 1) * 100;

                this.setState({timeout: setTimeout(() => this.acquireTheLock(), retryInSeconds)});
            }

            render() {
                const shouldShowSummary = this.props.showSummary;
                const locks = this.state.locks;
                const attempts = this.state.counter;
                const currentlyHolding = (this.props.holding == this.props.name);
                const textColour = 'text-3xl font-black text-'+this.props.colour+'-600';
                const divStyle = {
                    minHeight: 400+'px',
                };

                return (
                    <div className="text-center" style={divStyle}>
                        <h2 className={textColour}>Player: {this.props.name}</h2>
                        <p className="text-gray-500">locks acquired: {locks}</p>
                        <p className="text-gray-700">attempts: {attempts}</p>
                        {currentlyHolding && shouldShowSummary == false && <i className="mt-5 fas fa-lock text-yellow-400 fa-4x"></i>}
                        {shouldShowSummary && <h4 className="text-5xl font-black">{Math.round((locks/attempts)*100)}%</h4>}
                    </div>
                );
            }
        }

        const boardContainer = document.querySelector('#board');
        ReactDOM.render(React.createElement(Board), boardContainer);
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
