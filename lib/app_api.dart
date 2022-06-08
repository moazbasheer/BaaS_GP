import 'package:http/http.dart' as http;

class CallApi {
  final String _url = "http://baas-gp.herokuapp.com/api/";
  postData(data, apiUrl) async {
    var fullUrl = _url + apiUrl;
    return await http.post(
      Uri.parse(fullUrl),
      body: data,
    );
  }
}
