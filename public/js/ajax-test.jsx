var UserGist = React.createClass({
    getInitialState: function() {
        return {
            username: '',
            lastGistUrl: '',
            albums: []
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
                {this.state.showLogin && <FbLogin url={this.state.lastGistUrl}/>}
                {this.state.showAlbums && <FbAlbum necum={this.state.albums}/>}
            </div>
        )
    }
});

var OpenAlbums = React.createClass({
    getInitialState: function() {
        return {
            data: [],
            paging: {}
        };
    },

    componentDidMount: function() {
        $.get(this.props.source, function(result) {
            var pictures = result;
            console.log(result);
            if (this.isMounted()) {
                this.setState({
                    data: pictures.data,
                    paging: pictures.paging
                })
            }
        }.bind(this));
    },

    render: function() {
        return (
            <div>
                <a href="">albums</a>
                {this.state.data.map(function(photo) {
                    return (
                        <div key={photo.id}>
                            <FbPicture photo={photo}/>
                        </div>
                    );

                })}
                {this.state.paging.next && <NextPagePhotos next={this.state.paging.next}/>}

            </div>
        )
    }
});



var NextPagePhotos = React.createClass({
    showMorePhotos: function(){
        console.log("cum");
        React.render(
            <OpenAlbums source={"/sources/public/facebook-pic.php?next=" + this.props.next} />,
            document.getElementById('content')
        );

    },

   render: function() {
       return (
         <div>
             <a href="#" onClick={this.showMorePhotos}>NEXT</a>
         </div>
       );
   }
});

React.render(
    <UserGist source="/sources/public/facebook.php" />,
    document.getElementById('content')
);


