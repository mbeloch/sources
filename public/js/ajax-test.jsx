var UserGist = React.createClass({
    getInitialState: function() {
        return {
            username: '',
            lastGistUrl: ''
        };
    },

    componentDidMount: function() {
        $.get(this.props.source, function(result) {
            var lastGist = result;
            console.log(result);
            if (this.isMounted()) {
                if (lastGist.login){
                    this.setState({
                        lastGistUrl: lastGist.login,
                        showLogin: true
                    });
                }else {
                    this.setState({
                        showAlbums: true,
                        albums: lastGist.albums
                    });

                }

            }
        }.bind(this));
    },

    render: function() {
        return (
            <div>
                {this.state.showLogin && <FbLogin url={this.stat.lastGistUrl}/>}
                {this.state.showAlbums && <FbAlbum necum={this.state.albums}/>}
            </div>
        )
    }
});

React.render(
    <UserGist source="/sources/public/facebook.php" />,
    document.getElementById('content')
);


