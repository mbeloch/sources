var FbAlbum = React.createClass({
   render: function(){
       return (
           <div>
           {this.props.necum.map(function (album){
               return (
                   <div key={album.id}>{album.name}</div>
               );
           })}
           </div>
       );
   }
});

var FbLogin = React.createClass({
   render: function(){
       return(
           <a href={this.props.url}>FB LOGIN</a>
       );
   }
});
