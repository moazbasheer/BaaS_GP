// ignore_for_file: deprecated_member_use

import 'dart:convert';

import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter/scheduler.dart';
import 'package:learningdart/Login.dart';
import 'package:learningdart/Complaint.dart';
import 'package:learningdart/passengerProfilePage.dart';
//import 'package:flutter_polyline_points/flutter_polyline_points.dart';

import 'clientProfilePage.dart';
import 'map.dart';
import 'passengerProfilePage.dart';
import 'package:http/http.dart' as http;
import 'package:latlong2/latlong.dart';

//import 'Splash_Screen.dart';

Future<joinedTripsInfo> getAllTrips() async {
  final response;
  if (rolename == "client") {
    response = await http.get(
      Uri.parse('http://baas-gp.herokuapp.com/api/client/trips/joined'),
      headers: {
        'Content-Type': 'application/json; charset=UTF-8',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
    );
  } else {
    response = await http.get(
      Uri.parse('http://baas-gp.herokuapp.com/api/passenger/trips/joined'),
      headers: {
        'Content-Type': 'application/json; charset=UTF-8',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
    );
  }
  if (response.statusCode == 200) {
    return joinedTripsInfo.fromJson(jsonDecode(response.body));
  } else {
    throw Exception('Failed to find completed trips to complaint');
  }
}

class joinedTripsInfo {
  final bool status;
  var message;
  joinedTripsInfo({required this.status, required this.message});

  factory joinedTripsInfo.fromJson(Map<String, dynamic> json) {
    return joinedTripsInfo(
      status: json['status'],
      message: json['message'],
    );
  }
}

class joinedTripscomplaints extends StatefulWidget {
  final String? rolename;
  final String? email;
  final String? username;
  joinedTripscomplaints({
    Key? key,
    this.email,
    this.username,
    this.rolename,
  }) : super(key: key);

  @override
  State<joinedTripscomplaints> createState() => _joinedTripscomplaintsState();
}

class _joinedTripscomplaintsState extends State<joinedTripscomplaints> {
  Future<joinedTripsInfo>? tripInfo;
  TextEditingController editingController = TextEditingController();
  List<String>? trips;
  int currentIndex = -1;
  var duplicateTrips = <String>[];
  List<String> stops = [];
  var indexes = <int>[];
  @override
  void initState() {
    super.initState();
    tripInfo = null;
    trips = null;
    duplicateTrips.clear();
    indexes.clear();
  }

  Future<void> filterSearchResults(String query) async {
    List<String> dummySearchList = <String>[];
    dummySearchList.addAll(duplicateTrips);
    if (query.isNotEmpty) {
      List<String> dummyListData = <String>[];
      int index = 0;
      indexes.clear();
      dummySearchList.forEach((item) {
        if (item.contains(query)) {
          dummyListData.add(item);
          indexes.add(index);
        }
        index = index + 1;
      });
      setState(() {
        trips = dummyListData;
      });
    } else {
      indexes.clear();
      setState(() {
        trips = duplicateTrips;
      });
    }
  }

  Widget buildChild() {
    if (widget.rolename == "client") {
      return clientProfilePage(
        rolename: widget.rolename,
        email: widget.email,
        username: widget.username,
      );
    } else {
      return passengerProfilePage(
          rolename: widget.rolename,
          email: widget.email,
          username: widget.username);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        drawer: buildChild(),
        appBar: AppBar(
          title: Text("Completed trips Complaints"),
        ),
        body: Container(
            child: tripInfo == null ? buildTrips() : checkTripRespons()));
  }

  Widget buildTrips() {
    return Column(
      children: <Widget>[
        SizedBox(height: 10),
        Padding(
          padding: const EdgeInsets.all(10.0),
          child: TextField(
            onChanged: (value) {
              filterSearchResults(value);
            },
            controller: editingController,
            decoration: InputDecoration(
                labelText: "Search completed trips",
                hintText: "Enter your search",
                prefixIcon: Icon(Icons.search),
                border: OutlineInputBorder(
                    borderRadius: BorderRadius.all(Radius.circular(10.0)))),
          ),
        ),
        SizedBox(height: 10),
        Expanded(
            child: FutureBuilder<joinedTripsInfo>(
          future: getAllTrips(),
          builder:
              (BuildContext context, AsyncSnapshot<joinedTripsInfo> snapshot) {
            duplicateTrips.clear();
            if (snapshot.hasData && snapshot.data!.status == true) {
              for (int y = 0; y < snapshot.data!.message.length; y++) {
                String AllTripstops = "";
                for (int t = 0;
                    t < snapshot.data!.message[y]['stops'].length;
                    t++) {
                  if (t != snapshot.data!.message[y]['stops'].length - 1) {
                    AllTripstops = AllTripstops +
                        snapshot.data!.message[y]['stops'][t]['name'] +
                        " , ";
                  } else {
                    AllTripstops = AllTripstops +
                        snapshot.data!.message[y]['stops'][t]['name'];
                  }
                }
                stops.add(AllTripstops);
              }
              for (int i = 0; i < snapshot.data!.message.length; i++) {
                duplicateTrips.add('''
    Trip #${snapshot.data!.message[i]['id']}
    Source: ${snapshot.data!.message[i]['route']['source']}
    Destination: ${snapshot.data!.message[i]['route']['destination']}
    Stops: ${stops[i]}
    Datetime: ${snapshot.data!.message[i]['datetime']}
    Price: ${snapshot.data!.message[i]['price']} EGY
    Organization's name: ${snapshot.data!.message[i]['organization']['name']}''');
              }
              if (trips == null) {
                trips = duplicateTrips;
              }
              if (trips?.length != 0) {
                return ListView.builder(
                    controller: ScrollController(),
                    shrinkWrap: true,
                    itemCount: trips?.length,
                    itemBuilder: (BuildContext context, int index) {
                      return Padding(
                        padding: const EdgeInsets.all(15),
                        child: ListTile(
                          tileColor: Color.fromRGBO(61, 7, 255, 0.035),
                          title: Text(
                            trips?[index] as String,
                            style: const TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          subtitle: Row(
                            children: <Widget>[
                              Padding(
                                  padding: EdgeInsets.fromLTRB(0, 80, 0, 0)),
                              Row(
                                children: <Widget>[
                                  Padding(
                                      padding:
                                          EdgeInsets.fromLTRB(15, 50, 0, 0)),
                                  Text.rich(TextSpan(
                                      style: TextStyle(
                                        fontSize: 18,
                                      ),
                                      children: [
                                        TextSpan(
                                            style: TextStyle(
                                                color: Colors.blue,
                                                decoration:
                                                    TextDecoration.underline),
                                            text: "See Full Path",
                                            recognizer: TapGestureRecognizer()
                                              ..onTap = () {
                                                List<LatLng> stops = <LatLng>[];
                                                if (indexes.length == 0) {
                                                  for (int y = 0;
                                                      y <
                                                          snapshot
                                                              .data!
                                                              .message[index]
                                                                  ['stops']
                                                              .length;
                                                      y++) {
                                                    stops.add(LatLng(
                                                        double.parse(snapshot
                                                                    .data!
                                                                    .message[index]
                                                                ['stops'][y]
                                                            ['latitude']),
                                                        double.parse(snapshot
                                                                    .data!
                                                                    .message[index]
                                                                ['stops'][y]
                                                            ['longitude'])));
                                                  }
                                                } else {
                                                  for (int i = 0;
                                                      i <
                                                          snapshot
                                                              .data!
                                                              .message[indexes[
                                                                      index]]
                                                                  ['stops']
                                                              .length;
                                                      i++) {
                                                    stops.add(LatLng(
                                                        double.parse(
                                                            snapshot.data!.message[
                                                                        indexes[index]]
                                                                    ['stops'][i]
                                                                ['latitude']),
                                                        double.parse(snapshot
                                                                    .data!
                                                                    .message[indexes[index]]
                                                                ['stops'][i]
                                                            ['longitude'])));
                                                  }
                                                }
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(
                                                      builder: (context) =>
                                                          MyHomePage(
                                                            email: widget.email,
                                                            username:
                                                                widget.username,
                                                            rolename:
                                                                widget.rolename,
                                                            checkComingFrom:
                                                                false,
                                                            stops: stops,
                                                          )),
                                                );
                                              }),
                                      ])),
                                  Container(
                                    padding: const EdgeInsets.all(10),
                                    child: FlatButton(
                                        child: const Text('Complaint'),
                                        color: Colors.blue,
                                        onPressed: () {
                                          setState(() {
                                            currentIndex = index;
                                            tripInfo = getAllTrips();
                                          });
                                        }),
                                  ),
                                ],
                              )
                            ],
                          ),
                        ),
                      );
                    });
              } else {
                return Text(
                  'No completed trips found',
                  style: TextStyle(
                    fontWeight: FontWeight.normal,
                    color: Colors.grey,
                    fontSize: 20,
                  ),
                );
              }
            }
            SchedulerBinding.instance.addPostFrameCallback((_) {
              Text(
                'No completed trips found',
                style: TextStyle(
                  fontWeight: FontWeight.normal,
                  color: Colors.grey,
                  fontSize: 20,
                ),
              );
            });
            return Text('');
          },
        ))
      ],
    );
  }

  FutureBuilder<joinedTripsInfo> checkTripRespons() {
    return FutureBuilder<joinedTripsInfo>(
        future: tripInfo,
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            if (snapshot.data!.status == true) {
              if (snapshot.data!.message.length > 0) {
                SchedulerBinding.instance.addPostFrameCallback((_) {
                  Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (_) => MyStatefulWidget(
                                email: email,
                                username: username,
                                rolename: rolename,
                                index: snapshot.data!.message[currentIndex]
                                    ['id'],
                              )));
                });
              } else {
                return Text(
                  'No available Trips from Complaint',
                  style: TextStyle(
                    fontWeight: FontWeight.normal,
                    color: Colors.grey,
                    fontSize: 20,
                  ),
                );
              }
            } else if (snapshot.data!.status == false) {
              return Text(
                'No available Trips from Complaint',
                style: TextStyle(
                  fontWeight: FontWeight.normal,
                  color: Colors.grey,
                  fontSize: 20,
                ),
              );
            }
          }
          return Center(
            child: CircularProgressIndicator(color: Colors.blue),
            widthFactor: 25.0,
            heightFactor: 25.0,
          );
        });
  }
}
