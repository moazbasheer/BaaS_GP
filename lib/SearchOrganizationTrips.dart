// ignore_for_file: deprecated_member_use

import 'dart:convert';

import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter/scheduler.dart';
import 'package:learningdart/Login.dart';
import 'package:learningdart/passengerProfilePage.dart';
import 'package:latlong2/latlong.dart';
//import 'package:flutter_polyline_points/flutter_polyline_points.dart';

import 'passengerProfilePage.dart';
import 'map.dart';
import 'package:http/http.dart' as http;

//import 'Splash_Screen.dart';
Future<organizationTripsInfo> getAllTrips() async {
  final response = await http.get(
    Uri.parse('http://baas-gp.herokuapp.com/api/passenger/trips/inside'),
    headers: {
      'Content-Type': 'application/json; charset=UTF-8',
      'Accept': 'application/json',
      'Authorization': 'Bearer $token'
    },
  );
  if (response.statusCode == 200) {
    return organizationTripsInfo.fromJson(jsonDecode(response.body));
  } else {
    throw Exception('Failed to create album.');
  }
}

class organizationTripsInfo {
  final bool status;
  var message;
  organizationTripsInfo({required this.status, required this.message});

  factory organizationTripsInfo.fromJson(Map<String, dynamic> json) {
    return organizationTripsInfo(
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

class showOrganizationTrips extends StatefulWidget {
  final String? name;
  final String? email;
  final String? rolename;
  const showOrganizationTrips({Key? key, this.email, this.name, this.rolename})
      : super(key: key);
  @override
  State<showOrganizationTrips> createState() => _showOrganizationTripsState();
}

class _showOrganizationTripsState extends State<showOrganizationTrips> {
  Future<organizationTripsInfo>? tripInfo;
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: passengerProfilePage(
        email: widget.email,
        username: widget.name,
        rolename: widget.rolename,
      ),
      appBar: AppBar(
        title: Text("Organization\'s trips"),
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
                labelText: "Search organization's trips",
                hintText: "Enter your search",
                prefixIcon: Icon(Icons.search),
                border: OutlineInputBorder(
                    borderRadius: BorderRadius.all(Radius.circular(10.0)))),
          ),
        ),
        SizedBox(height: 10),
        Expanded(
            child: FutureBuilder<organizationTripsInfo>(
                future: getAllTrips(),
                builder: (BuildContext context,
                    AsyncSnapshot<organizationTripsInfo> snapshot) {
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
    Datetime: ${snapshot.data!.message[i]['datetime']}''');
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
                                                                              widget.name,
                                                                          rolename:
                                                                              widget.rolename,
                                                                          checkComingFrom:
                                                                              true,
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

  FutureBuilder<organizationTripsInfo> checkTripRespons() {
    return FutureBuilder<organizationTripsInfo>(
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
                                  builder: (_) => showOrganizationTrips(
                                        email: email,
                                        name: username,
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
                                builder: (_) => showOrganizationTrips(
                                      email: email,
                                      name: username,
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
    final response = await http.post(
        Uri.parse('http://baas-gp.herokuapp.com/api/passenger/trips/$index'),
        headers: {
          'Content-Type': 'application/json; charset=UTF-8',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token'
        },
        body: jsonEncode(<String, String>{'payment_method': 'wallet'}));
    if (response.statusCode == 200) {
      return joinAndCancellation.fromJson(jsonDecode(response.body));
    } else {
      throw Exception('Failed to join trip');
    }
  }

  Future<joinAndCancellation> cancelTrip(int index) async {
    final response = await http.post(
      Uri.parse(
          'http://baas-gp.herokuapp.com/api/passenger/trips/cancel/$index'),
      headers: {
        'Content-Type': 'application/json; charset=UTF-8',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
    );
    if (response.statusCode == 200) {
      return joinAndCancellation.fromJson(jsonDecode(response.body));
    } else {
      throw Exception('Failed to cancel trip');
    }
  }
}
