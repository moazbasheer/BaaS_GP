import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter_polyline_points/flutter_polyline_points.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:learningdart/Login.dart';
import 'package:learningdart/SearchOrganizationTrips.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:google_polyline_algorithm/google_polyline_algorithm.dart'
    show decodePolyline;
import 'package:http/http.dart' as http;
import 'package:learningdart/searchPublicTrips.dart';

class Album {
  num distance;
  num time;
  final String points;

  Album({
    required this.distance,
    required this.time,
    required this.points,
  });
  factory Album.fromJson(Map<String, dynamic> json) {
    return Album(
        distance: json['paths'][0]["distance"],
        time: json['paths'][0]["time"],
        points: json['paths'][0]["points"]);
  }
  String getPath() {
    return points;
  }
}

extension PolylineExt on List<List<num>> {
  List<LatLng> unpackPolyline() =>
      map((p) => LatLng(p[0].toDouble(), p[1].toDouble())).toList();
}

class MyHomePage extends StatefulWidget {
  static const String route = 'polyline';
  final String? email;
  final String? username;
  final String? rolename;
  final bool? checkComingFrom;
  final List<LatLng>? stops;
  MyHomePage(
      {Key? key,
      this.email,
      this.rolename,
      this.username,
      this.checkComingFrom,
      this.stops})
      : super(key: key);
  @override
  State<MyHomePage> createState() => _MyHomePageState();
}

class _MyHomePageState extends State<MyHomePage> {
  Future<Album>? futureAlbum;
  String word = "{livD}_o}Dk@C_BHeFZg@AcHyg@e@uE@kADGf@FdB^fItBvATpHbB";
  Future<List<Polyline>>? polylines;
  List<LatLng> polylineCoordinates = [];
  PolylinePoints polylinePoints = PolylinePoints();
  List<Marker> allMarkers = [];
  var polyline;
  Future<Album> fetchAlbum() async {
    var url =
        "https://graphhopper.com/api/1/route?key=6a180887-e6c2-4ded-9810-44edaa250e40&";
    for (int i = 0; i < widget.stops!.length; i++) {
      url += "point=" +
          (widget.stops?[i].latitude).toString() +
          "," +
          (widget.stops?[i].longitude).toString();
      if (i != (widget.stops!.length - 1)) {
        url += "&";
      }
    }
    final response = await http.get(
      Uri.parse(url),
    );
    if (response.statusCode == 200) {
      return await Album.fromJson(jsonDecode(response.body));
    } else {
      throw Exception('Failed to Build Path');
    }
  }

  Future<List<Polyline>> getPolylines(String pointsPath) async {
    polyline = await decodePolyline(pointsPath).unpackPolyline();
    allMarkers.add(
      Marker(
        point: LatLng(
          widget.stops![0].latitude,
          widget.stops![0].longitude,
        ),
        builder: (context) => const Icon(
          Icons.location_pin,
          color: Color.fromARGB(255, 33, 47, 243),
          size: 40.0,
        ),
      ),
    );
    allMarkers.add(
      Marker(
        point: LatLng(
          widget.stops![widget.stops!.length - 1].latitude,
          widget.stops![widget.stops!.length - 1].longitude,
        ),
        builder: (context) => const Icon(
          Icons.location_pin,
          color: Color.fromARGB(255, 84, 175, 76),
          size: 40.0,
        ),
      ),
    );
    for (var x = 1; x < widget.stops!.length - 1; x++) {
      allMarkers.add(
        Marker(
          point: LatLng(
            widget.stops![x].latitude,
            widget.stops![x].longitude,
          ),
          builder: (context) => const Icon(
            Icons.circle,
            color: Colors.red,
            size: 20.0,
          ),
        ),
      );
    }
    var polyLines = await [
      Polyline(
        points: polyline,
        strokeWidth: 4.0,
        color: Color.fromARGB(255, 0, 0, 0),
      ),
    ];
    //await Future.delayed(const Duration(seconds: 3));
    return polyLines;
  }

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: AppBar(
            leading: IconButton(
                icon: Icon(FontAwesomeIcons.arrowLeft),
                onPressed: () {
                  if (widget.checkComingFrom == true) {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (context) => showOrganizationTrips(
                                email: email,
                                name: username,
                                rolename: rolename,
                              )),
                    );
                  } else {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (context) => searchPublicTrips(
                                email: email,
                                username: username,
                                rolename: rolename,
                              )),
                    );
                  }
                }),
            title: const Text('The trip path')),
        body: Stack(children: <Widget>[
          Padding(
            padding: const EdgeInsets.all(3.0),
            child: FutureBuilder<Album>(
                future: fetchAlbum(),
                builder: (context, snapshot) {
                  return snapshot.hasData
                      ? FutureBuilder<List<Polyline>>(
                          future: getPolylines(snapshot.data!.points),
                          builder: (BuildContext context,
                              AsyncSnapshot<List<Polyline>> snapshot2) {
                            if (snapshot2.hasData) {
                              return Column(children: [
                                Flexible(
                                  child: FlutterMap(
                                    options: MapOptions(
                                      center: LatLng(widget.stops![0].latitude,
                                          widget.stops![0].longitude),
                                      zoom: 12.0,
                                    ),
                                    layers: [
                                      TileLayerOptions(
                                          urlTemplate:
                                              'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                                          subdomains: ['a', 'b', 'c']),
                                      PolylineLayerOptions(
                                        polylines: snapshot2.data!,
                                        polylineCulling: true,
                                      ),
                                      MarkerLayerOptions(
                                          markers: allMarkers.sublist(
                                              0, allMarkers.length)),
                                    ],
                                  ),
                                ),
                                Container(
                                    padding: const EdgeInsets.symmetric(
                                      vertical: 6.0,
                                      horizontal: 12.0,
                                    ),
                                    decoration: BoxDecoration(
                                      color: Colors.yellowAccent,
                                      borderRadius: BorderRadius.circular(20.0),
                                      boxShadow: const [
                                        BoxShadow(
                                          color: Colors.black26,
                                          offset: Offset(0, 2),
                                          blurRadius: 6.0,
                                        )
                                      ],
                                    ),
                                    child: Text(
                                      '${buildDistanceAndTime(snapshot.data!.time, snapshot.data!.distance)}',
                                      style: const TextStyle(
                                        fontSize: 18.0,
                                        fontWeight: FontWeight.w600,
                                      ),
                                    ))
                              ]);
                            }
                            return const Text('');
                          })
                      : Center(child: CircularProgressIndicator());
                }),
          ),
        ]));
  }

  String buildDistanceAndTime(var time, var distance) {
    var t = (time / 1000);
    String? DistAndTime = "";
    if (distance < 1000) {
      DistAndTime = DistAndTime + '$distance m , ';
    } else {
      distance = (distance / 1000);
      double distAfterModify = double.parse(distance.toStringAsFixed(2));
      DistAndTime = DistAndTime + '$distAfterModify Km , ';
    }
    if (time < 60) {
      return DistAndTime + 'less than 1 minute';
    } else {
      var hours = (t ~/ 60);
      var minutes = (hours % 60);
      if (time < 3600) {
        return DistAndTime + '$minutes min';
      } else {
        hours = (hours ~/ 60);
        return DistAndTime + '$hours hrs $minutes min';
      }
    }
  }
}
