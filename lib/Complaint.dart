// ignore_for_file: deprecated_member_use

import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter/scheduler.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:learningdart/Login.dart';

import 'joinedTripsComplaints.dart';
import 'package:http/http.dart' as http;

Future<complaintInfo> complaintTrips(String id, String message) async {
  final response;
  if (rolename == "client") {
    response = await http.post(
      Uri.parse('http://baas-gp.herokuapp.com/api/client/complaints'),
      headers: {
        'Content-Type': 'application/json; charset=UTF-8',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
      body: jsonEncode(<String, String>{'message': message, 'trip_id': id}),
    );
  } else {
    response = await http.post(
      Uri.parse('http://baas-gp.herokuapp.com/api/passenger/complaints'),
      headers: {
        'Content-Type': 'application/json; charset=UTF-8',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
      body: jsonEncode(<String, String>{'message': message, 'trip_id': id}),
    );
  }
  if (response.statusCode == 200) {
    return complaintInfo.fromJson(jsonDecode(response.body));
  } else {
    throw Exception('Failed to send Complaint');
  }
}

class complaintInfo {
  final bool status;
  final String message;
  complaintInfo({required this.status, required this.message});
  factory complaintInfo.fromJson(Map<String, dynamic> json) {
    return complaintInfo(
      status: json['status'],
      message: json['message'][0],
    );
  }
}

enum BestTutorSite { Choice1, Choice2, Choice3, other }

class MyStatefulWidget extends StatefulWidget {
  final int? index;
  final String? email;
  final String? username;
  final String? rolename;
  const MyStatefulWidget({
    Key? key,
    this.index,
    this.email,
    this.username,
    this.rolename,
  }) : super(key: key);

  @override
  _MyStatefulWidgetState createState() => _MyStatefulWidgetState();
}

class _MyStatefulWidgetState extends State<MyStatefulWidget> {
  TextEditingController messageController = TextEditingController();
  final GlobalKey<FormState> _formkey = GlobalKey<FormState>();
  BestTutorSite? _site;
  bool? isAcceptedvalue;
  bool? isTrue;
  Future<complaintInfo>? complaintsInfo;

  @override
  void initState() {
    super.initState();
    _site = null;
    isAcceptedvalue = false;
    isTrue = false;
  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
        debugShowCheckedModeBanner: false,
        home: Scaffold(
            appBar: AppBar(
              leading: IconButton(
                  icon: Icon(FontAwesomeIcons.arrowLeft),
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (context) => joinedTripscomplaints(
                                email: email,
                                username: username,
                                rolename: rolename,
                              )),
                    );
                  }),
              title: Text("Complaint", style: TextStyle(fontSize: 22)),
            ),
            body: SingleChildScrollView(
                controller: ScrollController(),
                padding: const EdgeInsets.fromLTRB(0, 10, 0, 10),
                child: complaintsInfo == null
                    ? buildComplaintForm()
                    : checkComplaintRespons())));
  }

  Widget buildComplaintForm() {
    return Form(
      key: _formkey,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: <Widget>[
          const Padding(
            padding: EdgeInsets.fromLTRB(20, 0, 0, 20),
            child: Text(
                "Please check the choices carefully\nThen choose one of them\nor choose other and write your complaint",
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  height: 1.75, // 1= 100%, were 0.9 = 90% of actual line height
                ),
                textAlign: TextAlign.start),
          ),
          //),
          ListTile(
            contentPadding: const EdgeInsets.fromLTRB(20, 0, 50, 10),
            title: const Text('The Driver didn\'t pick up me'),
            leading: Radio(
              value: BestTutorSite.Choice1,
              groupValue: _site,
              onChanged: (BestTutorSite? value) {
                setState(() {
                  _site = value;
                  isAcceptedvalue = true;
                  isTrue = false;
                });
              },
            ),
          ),
          ListTile(
            contentPadding: const EdgeInsets.fromLTRB(20, 0, 50, 10),
            title: const Text('i didn\'t feel safe with the driver'),
            leading: Radio(
              value: BestTutorSite.Choice2,
              groupValue: _site,
              onChanged: (BestTutorSite? value) {
                setState(() {
                  _site = value;
                  isAcceptedvalue = true;
                  isTrue = false;
                });
              },
            ),
          ),
          ListTile(
            contentPadding: const EdgeInsets.fromLTRB(20, 0, 50, 10),
            title: const Text('the driver didn\'t commit to the specific time'),
            leading: Radio(
              value: BestTutorSite.Choice3,
              groupValue: _site,
              onChanged: (BestTutorSite? value) {
                setState(() {
                  _site = value;
                  isAcceptedvalue = true;
                  isTrue = false;
                });
              },
            ),
          ),
          ListTile(
            contentPadding: const EdgeInsets.fromLTRB(20, 0, 50, 10),
            title: const Text('Other'),
            leading: Radio(
              value: BestTutorSite.other,
              groupValue: _site,
              onChanged: (BestTutorSite? value) {
                setState(() {
                  _site = value;
                  isAcceptedvalue = true;
                  isTrue = true;
                });
              },
            ),
          ),
          Padding(
            padding: const EdgeInsets.only(
                left: 20.0, right: 40.0, top: 20, bottom: 30),
            child: TextFormField(
              enabled: isTrue,
              keyboardType: TextInputType.text,
              controller: messageController,
              decoration: const InputDecoration(
                  //border: OutlineInputBorder(),
                  hintText: 'Write Something',
                  hintStyle: TextStyle(fontSize: 17)),
              validator: (value) {
                if (value!.isEmpty) {
                  return 'Please write something';
                }
                return null;
              },
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(100, 0, 100, 10),
            child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  minimumSize: const Size(150, 0),
                ),
                child: const Padding(
                  padding: EdgeInsets.all(10),
                  child: Text(
                    "Submit",
                    style: TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                ),
                onPressed: isAcceptedvalue == true
                    ? () {
                        if (_site != BestTutorSite.other) {
                          String message = "";
                          if (_site == BestTutorSite.Choice1) {
                            message = 'The Driver didn\'t pick up me';
                          } else if (_site == BestTutorSite.Choice2) {
                            message = 'i didn\'t feel safe with the driver';
                          } else {
                            message =
                                'the driver didn\'t commit to the specific time';
                          }
                          setState(() {
                            complaintsInfo = complaintTrips(
                                widget.index.toString(), message);
                          });
                        } else if (_formkey.currentState!.validate() &&
                            _site == BestTutorSite.other) {
                          String message = messageController.text;
                          setState(() {
                            complaintsInfo = complaintTrips(
                                widget.index.toString(), message);
                          });
                        }
                        //}
                      }
                    : null),
          ),
        ],
      ),
    );
  }

  FutureBuilder<complaintInfo> checkComplaintRespons() {
    return FutureBuilder<complaintInfo>(
        future: complaintsInfo,
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            if (snapshot.data!.status == true) {
              SchedulerBinding.instance.addPostFrameCallback((_) {
                Navigator.push(
                    context,
                    MaterialPageRoute(
                        builder: (_) => joinedTripscomplaints(
                              email: email,
                              username: username,
                              rolename: rolename,
                            )));
                Future.delayed(
                    Duration.zero,
                    () => showDialog(
                        context: context,
                        builder: (BuildContext context) => AlertDialog(
                              title: Text('${snapshot.data!.message}'),
                              content:
                                  const Text('It will take in consideration'),
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
            } else if (snapshot.data!.status == false) {
              SchedulerBinding.instance.addPostFrameCallback((_) {
                setState(() {
                  complaintsInfo = null;
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
}
