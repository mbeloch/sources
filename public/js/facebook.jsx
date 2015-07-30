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
var selectedPhotos = [];
var withoutBorder = '3px solid white';
var withBorder = '3px solid red';

var FbLogin = React.createClass({
    render: function () {
        return (
            <a href={this.props.url}>FB LOGIN</a>
        );
    }
});

var FbPicture = React.createClass({

    getInitialState: function(){
        var neco;
        if (this.props.photo){
            neco = this.props.photo.selected ? withBorder : withoutBorder;
        }


        return {
            style: {
                border: neco
            }
        }
    },

    handleClick: function () {
        React.render(
            <OpenAlbums albumId={this.props.album.id} />,
            document.getElementById('content')
        );
    },

    selectDeselect: function () {
        if(this.props.photo.selected){
            this.props.photo.selected = false;
            photoId = this.props.photo.id;
            _.remove(selectedPhotos, function(photo){
                return  photo.id == photoId;
            });
            console.log("deselected");
            this.setState({style:{border: withoutBorder}});
        }else {
            this.props.photo.selected = true;
            selectedPhotos.push(this.props.photo);
            this.setState({style:{border: withBorder}});
            console.log("selected");
        }
    },

    render: function () {
        if (this.props.photo){
            return (
                <div>
                    <div><img src={this.props.photo.images[this.props.photo.images.length-2].source} style={this.state.style} onClick={this.selectDeselect}/></div>
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
        var propsId = this.props.albumId;

        albumId = _.findIndex(fbUserContent.albums, function(album){
            return album.id == propsId;
        });

        if (!fbUserContent.albums[albumId].photos){
            $.get("/sources/public/facebook-pic.php?albumId=" + this.props.albumId, function(result) {
                var pictures = result;
                console.log(result);
                if (this.isMounted()) {
                    this.setState({
                        data: pictures.data,
                        paging: pictures.paging
                    })
                }
                fbUserContent.albums[albumId].photos = result;
            }.bind(this));
        }else {
            this.setState({
                data: fbUserContent.albums[albumId].photos.data,
                paging: fbUserContent.albums[albumId].photos.paging
            })
        }
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

    download: function(){
        var dataJson = {
            photos: selectedPhotos
        };
        $.ajax({
            url: '/sources/public/facebook-downl.php',
            dataType: 'json',
            type: 'POST',
            data: dataJson,
            success: function(data){
                selectedPhotos = [];
            }.bind(this),
            error: function(xhr, status, err) {
                console.error('/sources/public/facebook-downl.php', status, err.toString());
            }.bind(this)
        });
    },

    render: function() {
        var showDownload = false;
        if (selectedPhotos.length > 0){
            showDownload = true;
        }

        return (
            <div>
                {showDownload && <button onClick={this.download}>Download selected</button>}
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
        if (url.loginUrl == 'ok'){
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
        React.render(
            <FbLogin  url={window.fbLoginUrl}/>,
            document.getElementById('content')
        );
    }
}

loadPermissions();




