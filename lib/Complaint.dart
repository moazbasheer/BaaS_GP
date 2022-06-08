// ignore_for_file: deprecated_member_use

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:learningdart/Login.dart';
import 'package:learningdart/complaints.dart';
import 'package:learningdart/profilePage.dart';

enum BestTutorSite { javatpoint, w3schools, tutorialandexample, other }

class MyStatefulWidget extends StatefulWidget {
  const MyStatefulWidget({Key? key}) : super(key: key);

  @override
  _MyStatefulWidgetState createState() => _MyStatefulWidgetState();
}

class _MyStatefulWidgetState extends State<MyStatefulWidget> {
  final GlobalKey<FormState> _formkey = GlobalKey<FormState>();
  BestTutorSite? _site;
  bool? isAcceptedvalue;
  bool? isTrue;

  @override
  void initState() {
    _site = null;
    isAcceptedvalue = false;
    isTrue = false;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: AppBar(
          leading: IconButton(
              icon: Icon(FontAwesomeIcons.arrowLeft),
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => const Complaints()),
                );
              }),
          title: Text("Complaint"),
        ),
        body: SingleChildScrollView(
          padding: const EdgeInsets.fromLTRB(0, 10, 0, 10),
          child: Form(
            key: _formkey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: <Widget>[
                const Padding(
                  padding: EdgeInsets.fromLTRB(20, 0, 0, 20),
                  child: Text(
                      "Please check the choices carefully\nThen choose one of them\nor choose other and write your complaint",
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        height:
                            1.75, // 1= 100%, were 0.9 = 90% of actual line height
                      ),
                      textAlign: TextAlign.start),
                ),
                //),
                ListTile(
                  contentPadding: const EdgeInsets.fromLTRB(30, 0, 50, 10),
                  title: const Text('The Driver didn\'t pick up me'),
                  leading: Radio(
                    value: BestTutorSite.javatpoint,
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
                  contentPadding: const EdgeInsets.fromLTRB(30, 0, 50, 10),
                  title: const Text('i didn\'t feel safe with the driver'),
                  leading: Radio(
                    value: BestTutorSite.w3schools,
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
                  contentPadding: const EdgeInsets.fromLTRB(30, 0, 50, 10),
                  title: const Text(
                      'the driver didn\'t commit to the specific time'),
                  leading: Radio(
                    value: BestTutorSite.tutorialandexample,
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
                  contentPadding: const EdgeInsets.fromLTRB(30, 0, 50, 10),
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
                      left: 30.0, right: 40.0, top: 20, bottom: 30),
                  child: TextFormField(
                    enabled: isTrue,
                    keyboardType: TextInputType.text,
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
                        /*shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(24.0))*/
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
                      onPressed: isAcceptedvalue!
                          ? () {
                              if (_formkey.currentState!.validate() ||
                                  _site != BestTutorSite.other) {
                                //print("successful");
                                //Navigator.push(
                                //context,
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                      builder: (context) => const Complaints()),
                                );
                              }
                              //}
                            }
                          : null),
                ),
              ],
            ),
          ),
        ));
  }
}
