var FbAlbum = React.createClass({
    render: function () {
        return (
            <div>
                <FbPicture album={this.props.album}/>
            </div>
        );
    }
});

var fbUserContent = {};

var FbLogin = React.createClass({
    render: function () {
        return (
            <a href={this.props.url}>FB LOGIN</a>
        );
    }
});

var FbPicture = React.createClass({
    handleClick: function () {
        React.render(
            <OpenAlbums albumId={this.props.album.id} />,
            document.getElementById('content')
        );
    },

    smrdisClick: function () {

    },

    render: function () {
        if (this.props.photo){
            return (
                <div>
                    <div>{this.props.photo.id}</div>
                </div>
            )
        }
        if (this.props.album){
            return (
                <div>
                    <a href="#"><img src={this.props.album.image} onClick={this.handleClick}/></a>
                </div>
            )
        }
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
        var albumId;
        console.log(this.props.albumId);

        var propsId = this.props.albumId;

        albumId = _.findIndex(fbUserContent.albums, function(album){
            return album.id == propsId;
        });

        if (fbUserContent.albums[albumId].photos){
            console.log("smrdis");
        }



        $.get("/sources/public/facebook-pic.php?albumId=" + this.props.albumId, function(result) {
            var pictures = result;
            console.log(result);
            if (this.isMounted()) {
                this.setState({
                    data: pictures.data,
                    paging: pictures.paging
                })
            }
            fbUserContent.albums[albumId].photos = pictures.data;
        }.bind(this));
    },

    albumList: function(){
        React.render(
            <LoadAlbums />,
            document.getElementById('content')
        );
    },

    render: function() {
        return (
            <div>
                <a onClick={this.albumList}>back to albums</a>
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

var LoadAlbums = React.createClass({
    getInitialState: function() {
        return {
            data: [],
            paging: {}
        };
    },

    componentDidMount: function() {
        if (!fbUserContent.albums){
            $.get('/sources/public/facebook.php', function(result) {
                var albums = result;
                fbUserContent.albums = albums.albums;
                console.log(result);
                if (this.isMounted()) {
                    this.setState({
                        data: albums.albums
                        //paging: pictures.paging
                    })
                }
            }.bind(this));
        }else {
            this.setState({
                data: fbUserContent.albums
            })
        }


    },

    render: function() {
        return (
            <div>
                {this.state.data.map(function (album) {
                    return (
                        <div key={album.id}>
                            <div>{album.name}</div>
                            <div >
                                <FbAlbum album={album}/>
                            </div>
                        </div>
                    );
                })}
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

function loadPermissions(){
    $.get('/sources/public/facebook-perm.php', function(result) {
        var url = result;
        if (url.loginUrl = 'ok'){
            window.loggedIn = true;
        }else {
            window.fbLoginUrl = url.loginUrl;
        }
        console.log(result);
        loadFB();
    })
}

function loadFB(){
    if (window.loggedIn){
        React.render(
            <LoadAlbums />,
            document.getElementById('content')
        );
    }else {
        alert('neni lognuty');
        React.render(
            <FbLogin  url={window.fbLoginUr}/>,
            document.getElementById('content')
        );
    }
}

loadPermissions();




