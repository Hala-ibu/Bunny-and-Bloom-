var Constants = {
  get_api_base_url: function () {
    if(location.hostname == 'localhost'){
      return "http://localhost:8018/Bunny-And-Bloom-/backend/";
    } else {
      return "https://bunny-and-bloom-bcoki.ondigitalocean.app/backend/";
    }
  }
};