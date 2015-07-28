var FbAlbum = React.createClass({
    render: function () {
        return (
            <div>
                {this.props.necum.map(function (album) {
                    return (
                        <div key={album.id}>
                            <div>{album.name}</div>
                            <div >
                                <FbPicture album={album}/>
                            </div>
                        </div>
                    );
                })}
            </div>
        );
    }
});

var neco = 1;

function necum(){
    console.log(neco);
    neco++;
}

var FbLogin = React.createClass({
    render: function () {
        return (
            <a href={this.props.url}>FB LOGIN</a>
        );
    }
});

var FbPicture = React.createClass({
    handleClick: function () {
        necum();
        React.render(
            <OpenAlbums source={"/sources/public/facebook-pic.php?albumId=" + this.props.album.id} />,
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
