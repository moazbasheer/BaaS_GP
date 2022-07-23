// ignore_for_file: deprecated_member_use

import 'dart:convert';

import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter/scheduler.dart';
import 'package:learningdart/Login.dart';
import 'package:learningdart/passengerProfilePage.dart';
import 'package:latlong2/latlong.dart';
//import 'package:flutter_polyline_points/flutter_polyline_points.dart';

import 'clientProfilePage.dart';
import 'passengerProfilePage.dart';
import 'map.dart';
//import 'Splash_Screen.dart';
import 'package:http/http.dart' as http;

Future<tripsInfo> getAllTrips() async {
  final response;
  if (rolename == "client") {
    response = await http.get(
      Uri.parse('http://baas-gp.herokuapp.com/api/client/trips'),
      headers: {
        'Content-Type': 'application/json; charset=UTF-8',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
    );
  } else {
    response = await http.get(
      Uri.parse('http://baas-gp.herokuapp.com/api/passenger/trips/outside'),
      headers: {
        'Content-Type': 'application/json; charset=UTF-8',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
    );
  }
  if (response.statusCode == 200) {
    return tripsInfo.fromJson(jsonDecode(response.body));
  } else {
    throw Exception('Failed to find trips');
  }
}

class tripsInfo {
  final bool status;
  var message;
  tripsInfo({required this.status, required this.message});

  factory tripsInfo.fromJson(Map<String, dynamic> json) {
    return tripsInfo(
      status: json['status'],
      message: json['message'],
    );
  }
}

class joinAndCancellation {
  final bool status;
  final String message;
  joinAndCancellation({required this.status, required this.message});
  factory joinAndCancellation.fromJson(Map<String, dynamic> json) {
    return joinAndCancellation(
      status: json['status'],
      message: json['message'][0],
    );
  }
}

class searchPublicTrips extends StatefulWidget {
  final String? rolename;
  final String? username;
  final String? email;
  searchPublicTrips({Key? key, this.email, this.username, this.rolename})
      : super(key: key);
  @override
  State<searchPublicTrips> createState() => _searchPublicTripsState();
}

class _searchPublicTripsState extends State<searchPublicTrips> {
  Future<tripsInfo>? tripInfo;
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
        title: Text("search trips"),
      ),
      body: Container(
          child: tripInfo == null ? buildTrips() : checkTripRespons()),
    );
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
                labelText: "Search trips",
                hintText: "Enter your search",
                prefixIcon: Icon(Icons.search),
                border: OutlineInputBorder(
                    borderRadius: BorderRadius.all(Radius.circular(10.0)))),
          ),
        ),
        SizedBox(height: 10),
        Expanded(
            child: FutureBuilder<tripsInfo>(
                future: getAllTrips(),
                builder:
                    (BuildContext context, AsyncSnapshot<tripsInfo> snapshot) {
                  duplicateTrips.clear();
                  if (snapshot.hasData) {
                    for (int y = 0; y < snapshot.data!.message.length; y++) {
                      String AllTripstops = "";
                      for (int t = 0;
                          t < snapshot.data!.message[y]['stops'].length;
                          t++) {
                        if (t !=
                            snapshot.data!.message[y]['stops'].length - 1) {
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
                                        padding:
                                            EdgeInsets.fromLTRB(0, 80, 0, 0)),
                                    Row(
                                      children: <Widget>[
                                        Padding(
                                            padding: EdgeInsets.fromLTRB(
                                                15, 50, 0, 0)),
                                        Text.rich(TextSpan(
                                            style: TextStyle(
                                              fontSize: 18,
                                            ),
                                            children: [
                                              TextSpan(
                                                  style: TextStyle(
                                                      color: Colors.blue,
                                                      decoration: TextDecoration
                                                          .underline),
                                                  text: "See Full Path",
                                                  recognizer:
                                                      TapGestureRecognizer()
                                                        ..onTap = () {
                                                          List<LatLng> stops =
                                                              <LatLng>[];
                                                          if (indexes.length ==
                                                              0) {
                                                            for (int y = 0;
                                                                y <
                                                                    snapshot
                                                                        .data!
                                                                        .message[
                                                                            index]
                                                                            [
                                                                            'stops']
                                                                        .length;
                                                                y++) {
                                                              stops.add(LatLng(
                                                                  double.parse(snapshot
                                                                          .data!
                                                                          .message[index]['stops'][y]
                                                                      [
                                                                      'latitude']),
                                                                  double.parse(snapshot
                                                                          .data!
                                                                          .message[index]['stops'][y]
                                                                      [
                                                                      'longitude'])));
                                                            }
                                                          } else {
                                                            for (int i = 0;
                                                                i <
                                                                    snapshot
                                                                        .data!
                                                                        .message[
                                                                            indexes[index]]
                                                                            [
                                                                            'stops']
                                                                        .length;
                                                                i++) {
                                                              stops.add(LatLng(
                                                                  double.parse(snapshot
                                                                          .data!
                                                                          .message[indexes[index]]['stops'][i]
                                                                      [
                                                                      'latitude']),
                                                                  double.parse(snapshot
                                                                          .data!
                                                                          .message[indexes[index]]['stops'][i]
                                                                      ['longitude'])));
                                                            }
                                                          }
                                                          Navigator.push(
                                                            context,
                                                            MaterialPageRoute(
                                                                builder:
                                                                    (context) =>
                                                                        MyHomePage(
                                                                          email:
                                                                              widget.email,
                                                                          username:
                                                                              widget.username,
                                                                          rolename:
                                                                              widget.rolename,
                                                                          checkComingFrom:
                                                                              false,
                                                                          stops:
                                                                              stops,
                                                                        )),
                                                          );
                                                        }),
                                            ])),
                                        Container(
                                          padding: const EdgeInsets.all(10),
                                          child: FlatButton(
                                              child: indexes.length == 0
                                                  ? snapshot.data!.message[index]
                                                              ['joined'] ==
                                                          true
                                                      ? const Text("Cancel")
                                                      : const Text("Join")
                                                  : snapshot.data!.message[
                                                                  indexes[index]]
                                                              ['joined'] ==
                                                          true
                                                      ? const Text('cancel')
                                                      : const Text('join'),
                                              color: indexes.length == 0
                                                  ? snapshot.data!.message[
                                                                  index]
                                                              ['joined'] ==
                                                          true
                                                      ? Colors.red
                                                      : Colors.blue
                                                  : snapshot.data!.message[
                                                                  indexes[
                                                                      index]]
                                                              ['joined'] ==
                                                          true
                                                      ? Colors.red
                                                      : Colors.blue,
                                              onPressed: () {
                                                if (indexes.length == 0) {
                                                  if (snapshot.data!
                                                              .message[index]
                                                          ['joined'] ==
                                                      true) {
                                                    Future.delayed(
                                                        Duration.zero,
                                                        () => showDialog(
                                                            context: context,
                                                            builder: (BuildContext
                                                                    context) =>
                                                                AlertDialog(
                                                                  title: const Text(
                                                                      'Are you want to cancel this trip ?'),
                                                                  content:
                                                                      const Text(
                                                                          'We will deposite the money to your wallet if you confirmed'),
                                                                  actions: <
                                                                      Widget>[
                                                                    TextButton(
                                                                      onPressed:
                                                                          () {
                                                                        Navigator.pop(
                                                                            context,
                                                                            'ok');
                                                                        setState(
                                                                            () {
                                                                          currentIndex =
                                                                              index;
                                                                          tripInfo =
                                                                              getAllTrips();
                                                                        });
                                                                      },
                                                                      child: const Text(
                                                                          'Ok'),
                                                                    ),
                                                                    TextButton(
                                                                      onPressed:
                                                                          () {
                                                                        Navigator.pop(
                                                                            context,
                                                                            'cancel');
                                                                      },
                                                                      child: const Text(
                                                                          'Cancel'),
                                                                    ),
                                                                  ],
                                                                )));
                                                  } else {
                                                    Future.delayed(
                                                        Duration.zero,
                                                        () => showDialog(
                                                            context: context,
                                                            builder: (BuildContext
                                                                    context) =>
                                                                AlertDialog(
                                                                  title: const Text(
                                                                      'Are you want to join this trip ?'),
                                                                  content:
                                                                      const Text(
                                                                          'We will withdraw from your wallet if you confirmed'),
                                                                  actions: <
                                                                      Widget>[
                                                                    TextButton(
                                                                      onPressed:
                                                                          () {
                                                                        Navigator.pop(
                                                                            context,
                                                                            'ok');
                                                                        setState(
                                                                            () {
                                                                          currentIndex =
                                                                              index;
                                                                          tripInfo =
                                                                              getAllTrips();
                                                                        });
                                                                      },
                                                                      child: const Text(
                                                                          'Ok'),
                                                                    ),
                                                                    TextButton(
                                                                      onPressed:
                                                                          () {
                                                                        Navigator.pop(
                                                                            context,
                                                                            'cancel');
                                                                      },
                                                                      child: const Text(
                                                                          'Cancel'),
                                                                    ),
                                                                  ],
                                                                )));
                                                  }
                                                } else {
                                                  if (snapshot.data!.message[
                                                              indexes[index]]
                                                          ['joined'] ==
                                                      true) {
                                                    Future.delayed(
                                                        Duration.zero,
                                                        () => showDialog(
                                                            context: context,
                                                            builder: (BuildContext
                                                                    context) =>
                                                                AlertDialog(
                                                                  title: const Text(
                                                                      'Are you want to cancel this trip ?'),
                                                                  content:
                                                                      const Text(
                                                                          'We will deposite the money to your wallet if you confirmed'),
                                                                  actions: <
                                                                      Widget>[
                                                                    TextButton(
                                                                      onPressed:
                                                                          () {
                                                                        Navigator.pop(
                                                                            context,
                                                                            'ok');
                                                                        setState(
                                                                            () {
                                                                          currentIndex =
                                                                              index;
                                                                          tripInfo =
                                                                              getAllTrips();
                                                                        });
                                                                      },
                                                                      child: const Text(
                                                                          'Ok'),
                                                                    ),
                                                                    TextButton(
                                                                      onPressed:
                                                                          () {
                                                                        Navigator.pop(
                                                                            context,
                                                                            'cancel');
                                                                      },
                                                                      child: const Text(
                                                                          'Cancel'),
                                                                    ),
                                                                  ],
                                                                )));
                                                  } else {
                                                    Future.delayed(
                                                        Duration.zero,
                                                        () => showDialog(
                                                            context: context,
                                                            builder: (BuildContext
                                                                    context) =>
                                                                AlertDialog(
                                                                  title: const Text(
                                                                      'Are you want to join this trip ?'),
                                                                  content:
                                                                      const Text(
                                                                          'We will withdraw from your wallet if you confirmed'),
                                                                  actions: <
                                                                      Widget>[
                                                                    TextButton(
                                                                      onPressed:
                                                                          () {
                                                                        Navigator.pop(
                                                                            context,
                                                                            'ok');
                                                                        setState(
                                                                            () {
                                                                          currentIndex =
                                                                              index;
                                                                          tripInfo =
                                                                              getAllTrips();
                                                                        });
                                                                      },
                                                                      child: const Text(
                                                                          'Ok'),
                                                                    ),
                                                                    TextButton(
                                                                      onPressed:
                                                                          () {
                                                                        Navigator.pop(
                                                                            context,
                                                                            'cancel');
                                                                      },
                                                                      child: const Text(
                                                                          'Cancel'),
                                                                    ),
                                                                  ],
                                                                )));
                                                  }
                                                }
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
                        'No available trips found',
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
                      'No available trips found',
                      style: TextStyle(
                        fontWeight: FontWeight.normal,
                        color: Colors.grey,
                        fontSize: 20,
                      ),
                    );
                  });
                  return Text('');
                }))
      ],
    );
  }

  FutureBuilder<tripsInfo> checkTripRespons() {
    return FutureBuilder<tripsInfo>(
        future: tripInfo,
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            if (snapshot.data!.status == true) {
              if (!snapshot.data!.message[currentIndex]['joined']) {
                return FutureBuilder<joinAndCancellation>(
                    future:
                        joinTrip(snapshot.data!.message[currentIndex]['id']),
                    builder: (BuildContext context,
                        AsyncSnapshot<joinAndCancellation> snapshot2) {
                      if (snapshot2.hasData) {
                        SchedulerBinding.instance.addPostFrameCallback((_) {
                          Navigator.push(
                              context,
                              MaterialPageRoute(
                                  builder: (_) => searchPublicTrips(
                                        email: email,
                                        username: username,
                                        rolename: rolename,
                                      )));
                          Future.delayed(
                              Duration.zero,
                              () => showDialog(
                                  context: context,
                                  builder: (BuildContext context) =>
                                      AlertDialog(
                                        title:
                                            Text('${snapshot2.data!.message}'),
                                        actions: <Widget>[
                                          TextButton(
                                            onPressed: () {
                                              Navigator.pop(context, 'ok');
                                            },
                                            child: const Text('Ok'),
                                          ),
                                        ],
                                      )));
                        });
                      }
                      return Text('');
                    });
              } else {
                return FutureBuilder<joinAndCancellation>(
                  future:
                      cancelTrip(snapshot.data!.message[currentIndex]['id']),
                  builder: (BuildContext context,
                      AsyncSnapshot<joinAndCancellation> snapshot3) {
                    if (snapshot3.hasData) {
                      SchedulerBinding.instance.addPostFrameCallback((_) {
                        Navigator.push(
                            context,
                            MaterialPageRoute(
                                builder: (_) => searchPublicTrips(
                                      email: email,
                                      username: username,
                                      rolename: rolename,
                                    )));
                        Future.delayed(
                            Duration.zero,
                            () => showDialog(
                                context: context,
                                builder: (BuildContext context) => AlertDialog(
                                      title: Text('${snapshot3.data!.message}'),
                                      actions: <Widget>[
                                        TextButton(
                                          onPressed: () {
                                            Navigator.pop(context, 'ok');
                                          },
                                          child: const Text('Ok'),
                                        ),
                                      ],
                                    )));
                      });
                    }
                    return Text('');
                  },
                );
              }
            } else if (snapshot.data!.status == false) {
              SchedulerBinding.instance.addPostFrameCallback((_) {
                setState(() {
                  trips?.clear();
                  duplicateTrips.clear();
                  tripInfo = null;
                });
              });
            }
          }
          return Center(
            child: CircularProgressIndicator(color: Colors.blue),
            widthFactor: 25.0,
            heightFactor: 25.0,
          );
        });
  }

  Future<joinAndCancellation> joinTrip(int index) async {
    final response;
    if (rolename == "client") {
      response = await http.post(
          Uri.parse('http://baas-gp.herokuapp.com/api/client/trips/$index'),
          headers: {
            'Content-Type': 'application/json; charset=UTF-8',
            'Accept': 'application/json',
            'Authorization': 'Bearer $token'
          },
          body: jsonEncode(<String, String>{'payment_method': 'wallet'}));
    } else {
      response = await http.post(
          Uri.parse('http://baas-gp.herokuapp.com/api/passenger/trips/$index'),
          headers: {
            'Content-Type': 'application/json; charset=UTF-8',
            'Accept': 'application/json',
            'Authorization': 'Bearer $token'
          },
          body: jsonEncode(<String, String>{'payment_method': 'wallet'}));
    }
    if (response.statusCode == 200) {
      return joinAndCancellation.fromJson(jsonDecode(response.body));
    } else {
      throw Exception('Failed to create album.');
    }
  }

  Future<joinAndCancellation> cancelTrip(int index) async {
    final response;
    if (rolename == "client") {
      response = await http.post(
        Uri.parse(
            'http://baas-gp.herokuapp.com/api/client/trips/cancel/$index'),
        headers: {
          'Content-Type': 'application/json; charset=UTF-8',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token'
        },
      );
    } else {
      response = await http.post(
        Uri.parse(
            'http://baas-gp.herokuapp.com/api/passenger/trips/cancel/$index'),
        headers: {
          'Content-Type': 'application/json; charset=UTF-8',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token'
        },
      );
    }
    if (response.statusCode == 200) {
      return joinAndCancellation.fromJson(jsonDecode(response.body));
    } else {
      throw Exception('Failed to create album.');
    }
  }
}
