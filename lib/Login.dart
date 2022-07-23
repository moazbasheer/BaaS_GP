// ignore_for_file: deprecated_member_use, file_names, sized_box_for_whitespace

import 'dart:convert';

import 'package:auto_size_text/auto_size_text.dart';
import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter/scheduler.dart';
import 'package:learningdart/SearchOrganizationTrips.dart';
import 'package:learningdart/searchPublicTrips.dart';
import 'Register.dart';
import 'widgets/header_widget.dart';
import 'package:http/http.dart' as http;

String? rolename;
String? username;
String? email;
String? token;
Future<loginInfo> LoginAccount(Map data) async {
  final response = await http.post(
    Uri.parse('http://baas-gp.herokuapp.com/api/login'),
    headers: <String, String>{
      'Content-Type': 'application/json; charset=UTF-8',
    },
    body: jsonEncode(<String, String>{
      'emailorusername': data['emailorusername'],
      'password': data['password'],
    }),
  );
  if (response.statusCode == 200) {
    var result = jsonDecode(response.body);
    if (result['status'] == true) {
      rolename = result['user']['role_name'];
      if (rolename == "passenger") {
        username = result['user']['name'];
      } else {
        username = result['user']['username'];
      }
      email = result['user']['email'];
      token = result['token'];
    }
    return loginInfo.fromJson(jsonDecode(response.body));
  } else {
    throw Exception('Failed to create album.');
  }
}

class loginInfo {
  final bool status;
  final String message;
  const loginInfo({required this.status, required this.message});

  factory loginInfo.fromJson(Map<String, dynamic> json) {
    return loginInfo(
      status: json['status'],
      message: json['message'][0],
    );
  }
}

class LoginDemo extends StatefulWidget {
  const LoginDemo({Key? key}) : super(key: key);

  @override
  _LoginDemoState createState() => _LoginDemoState();
}

class _LoginDemoState extends State<LoginDemo> {
  String? erorrMessage;
  bool isPasswordVisible = true;
  final GlobalKey<FormState> _formkey = GlobalKey<FormState>();
  TextEditingController emailorusernameController = TextEditingController();
  TextEditingController passwordController = TextEditingController();
  Future<loginInfo>? login;
  Map _login() {
    Map data = {
      'emailorusername': emailorusernameController.text,
      'password': passwordController.text,
    };
    return data;
  }

  @override
  void initState() {
    super.initState();
  }

  double _headerHeight = 200;
  @override
  Widget build(BuildContext context) {
    return Scaffold(
        backgroundColor: Colors.white,
        body: SingleChildScrollView(
          controller: ScrollController(),
          child: Column(children: <Widget>[
            errorAlert(),
            if (login == null) buildlogin() else checkLoginRespons()
          ]),
        ));
  }

  Widget errorAlert() {
    if (erorrMessage != null) {
      return Container(
        color: Colors.red,
        width: double.infinity,
        padding: EdgeInsets.all(8),
        child: Row(
          children: <Widget>[
            Padding(
              padding: const EdgeInsets.only(right: 8.0),
              child: Icon(Icons.error_outline),
            ),
            Expanded(
              child: AutoSizeText(
                'This email is ' +
                    erorrMessage! +
                    ' or the password is incorrect',
                maxLines: 3,
              ),
            ),
            Padding(
              padding: const EdgeInsets.only(left: 8.0),
              child: IconButton(
                icon: Icon(Icons.close),
                onPressed: () {
                  setState(() {
                    erorrMessage = null;
                  });
                },
              ),
            )
          ],
        ),
      );
    }
    return SizedBox(
      height: 0,
    );
  }

  Form buildlogin() {
    return Form(
      key: _formkey,
      child: Column(
        children: <Widget>[
          SizedBox(
            height: _headerHeight,
            child: HeaderWidget(_headerHeight, true, Icons.password_rounded),
          ),
          Padding(
            padding: const EdgeInsets.only(top: 10.0),
            child: Column(
              children: <Widget>[
                SizedBox(
                    width: 200,
                    height: 200,
                    child: Image.asset(
                      'images/logo.png',
                      fit: BoxFit.fitWidth,
                      color: Colors.white.withOpacity(1),
                      colorBlendMode: BlendMode.modulate,
                    )),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.only(
                left: 40.0, right: 40.0, top: 0, bottom: 10),
            child: TextFormField(
              controller: emailorusernameController,
              keyboardType: TextInputType.text,
              decoration: const InputDecoration(
                  prefixIcon: Icon(Icons.person),
                  //border: OutlineInputBorder(), //labelText: 'Password',
                  hintText: 'Username or Email',
                  hintStyle: TextStyle(fontSize: 14)),
              validator: (value) {
                if (value!.isEmpty) {
                  return 'Please Enter Username';
                }
                return null;
              },
            ),
          ),
          Padding(
            padding: const EdgeInsets.only(
                left: 40.0, right: 40.0, top: 15, bottom: 10),
            child: TextFormField(
              controller: passwordController,
              keyboardType: TextInputType.text,
              obscureText: isPasswordVisible,
              decoration: InputDecoration(
                  prefixIcon: const Icon(Icons.lock),
                  suffixIcon: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 8.0),
                    child: IconButton(
                      onPressed: () {
                        setState(() {
                          isPasswordVisible = !isPasswordVisible;
                        });
                      },
                      icon: Icon(
                        isPasswordVisible
                            ? Icons.visibility
                            : Icons.visibility_off,
                        color: const Color.fromARGB(255, 168, 159, 159),
                      ),
                    ),
                  ),
                  hintText: 'Password',
                  hintStyle: const TextStyle(fontSize: 14)),
              validator: (value) {
                if (value!.isEmpty) {
                  return 'Please Enter password';
                }
                return null;
              },
            ),
          ),
          SizedBox(
            height: 20,
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
                minimumSize: const Size(290, 0),
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(24.0))),
            child: const Padding(
              padding: EdgeInsets.fromLTRB(20, 10, 0, 10),
              child: Text(
                "Login",
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
            ),
            onPressed: () {
              if (_formkey.currentState!.validate()) {
                setState(() {
                  login = LoginAccount(_login());
                });
              }
            },
          ),
          const SizedBox(
            height: 30,
          ),
          //const Text('New User? create account'),
          Text.rich(TextSpan(children: [
            const TextSpan(text: "New User? "),
            TextSpan(
              text: 'Create account',
              recognizer: TapGestureRecognizer()
                ..onTap = () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                        builder: (context) => const RegisterDemo()),
                  );
                },
              style: const TextStyle(fontWeight: FontWeight.bold),
            ),
          ]))
        ],
      ),
    );
  }

  FutureBuilder<loginInfo> checkLoginRespons() {
    return FutureBuilder<loginInfo>(
        future: login,
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            if (snapshot.data!.status == true) {
              SchedulerBinding.instance.addPostFrameCallback((_) {
                if (rolename == "passenger") {
                  Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (_) => showOrganizationTrips(
                                email: email,
                                name: username,
                                rolename: rolename,
                              )));
                } else {
                  Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (_) => searchPublicTrips(
                                email: email,
                                username: username,
                                rolename: rolename,
                              )));
                }
              });
            } else if (snapshot.data!.status == false) {
              SchedulerBinding.instance.addPostFrameCallback((_) {
                setState(() {
                  erorrMessage = snapshot.data!.message;
                  login = null;
                  emailorusernameController.clear();
                  passwordController.clear();
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
