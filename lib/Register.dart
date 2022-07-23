// ignore_for_file: file_names

import 'dart:convert';

import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter/scheduler.dart';
import 'package:flutter/services.dart';
import 'package:auto_size_text/auto_size_text.dart';
import 'Login.dart';
import 'widgets/header_widget.dart';
import 'package:http/http.dart' as http;

Future<registerInfo> RegisterAccount(Map data) async {
  final response = await http.post(
    Uri.parse('http://baas-gp.herokuapp.com/api/register'),
    headers: <String, String>{
      'Content-Type': 'application/json; charset=UTF-8',
    },
    body: jsonEncode(<String, String>{
      'email': data['email'],
      'password': data['password'],
      'role_name': 'client',
      'phone_number': data['phone_number'],
      'first_name': data['first_name'],
      'last_name': data['last_name'],
      'username': data['username'],
      'name': '',
      'postal_code': '',
      'address': ''
    }),
  );
  if (response.statusCode == 200) {
    return registerInfo.fromJson(jsonDecode(response.body));
  } else {
    throw Exception('Failed to create new account');
  }
}

class registerInfo {
  final bool status;
  final String message;
  const registerInfo({
    required this.status,
    required this.message,
  });

  factory registerInfo.fromJson(Map<String, dynamic> json) {
    return registerInfo(
      status: json['status'],
      message: json['message'][0],
    );
  }
}

class RegisterDemo extends StatefulWidget {
  const RegisterDemo({Key? key}) : super(key: key);

  @override
  _RegisterDemoState createState() => _RegisterDemoState();
}

class _RegisterDemoState extends State<RegisterDemo> {
  String? erorrMessage;
  bool isPasswordVisible = true;
  bool checkboxValue = false;
  Future<registerInfo>? register;
  final GlobalKey<FormState> _formkey = GlobalKey<FormState>();
  TextEditingController firstnameController = TextEditingController();
  TextEditingController lastnameController = TextEditingController();
  TextEditingController usernameController = TextEditingController();
  TextEditingController phonenumberController = TextEditingController();
  TextEditingController emailController = TextEditingController();
  TextEditingController passwordController = TextEditingController();
  TextEditingController confirmpasswordController = TextEditingController();
  Map _register() {
    Map data = {
      'email': emailController.text,
      'password': passwordController.text,
      'role_name': 'client',
      'phone_number': phonenumberController.text,
      'first_name': firstnameController.text,
      'last_name': lastnameController.text,
      'username': usernameController.text,
      'name': '',
      'postal_code': '',
      'address': ''
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
            child: Column(
              children: <Widget>[
                showAlert(),
                if (register == null) buildForm() else checkRegisterRespons(),
              ],
            )));
  }

  Widget showAlert() {
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
                erorrMessage!,
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

  Form buildForm() {
    return Form(
      key: _formkey,
      child: Column(
        children: <Widget>[
          SizedBox(
            height: _headerHeight,
            child: HeaderWidget(_headerHeight, true, Icons.password_rounded),
          ),
          Padding(
            padding: const EdgeInsets.only(top: 30.0),
            child: Column(
              children: <Widget>[
                SizedBox(
                    width: 200,
                    height: 200,
                    /*decoration: BoxDecoration(
                        color: Colors.red,
                        borderRadius: BorderRadius.circular(50.0)),*/
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
              controller: firstnameController,
              keyboardType: TextInputType.name,
              decoration: const InputDecoration(
                  //border: OutlineInputBorder(), //labelText: 'Password',
                  hintText: 'firstName',
                  hintStyle: TextStyle(fontSize: 14)),
              validator: (value) {
                if (value!.isEmpty) {
                  return 'Please Enter firstName';
                }
                return null;
              },
            ),
          ),
          Padding(
            padding: const EdgeInsets.only(
                left: 40.0, right: 40.0, top: 15, bottom: 10),
            child: TextFormField(
              controller: lastnameController,
              keyboardType: TextInputType.name,
              decoration: const InputDecoration(
                  //border: OutlineInputBorder(), //labelText: 'Password',
                  hintText: 'lastName',
                  hintStyle: TextStyle(fontSize: 14)),
              validator: (value) {
                if (value!.isEmpty) {
                  return 'Please Enter lastName';
                }
                return null;
              },
            ),
          ),
          Padding(
            padding: const EdgeInsets.only(
                left: 40.0, right: 40.0, top: 15, bottom: 10),
            child: TextFormField(
              controller: phonenumberController,
              keyboardType: TextInputType.phone,
              inputFormatters: [FilteringTextInputFormatter.digitsOnly],
              decoration: const InputDecoration(
                  prefixIcon: Icon(Icons.phone),
                  //border: OutlineInputBorder(), //labelText: 'Password',
                  hintText: 'phoneNumber',
                  hintStyle: TextStyle(fontSize: 14)),
              validator: (value) {
                if (value!.isEmpty) {
                  return 'Please Enter phone number';
                }
                if (!RegExp(r'^\+?0[0-9]{10}$').hasMatch(value)) {
                  return 'Please a valid phone number';
                }
                return null;
              },
            ),
          ),
          Padding(
            padding: const EdgeInsets.only(
                left: 40.0, right: 40.0, top: 15, bottom: 10),
            child: TextFormField(
              controller: emailController,
              keyboardType: TextInputType.emailAddress,
              decoration: const InputDecoration(
                  prefixIcon: Icon(Icons.email),
                  //border: OutlineInputBorder(),
                  //labelText: 'Email',
                  hintText: 'Email',
                  hintStyle: TextStyle(fontSize: 14)),
              validator: (value) {
                if (value!.isEmpty) {
                  return 'Please Enter a email';
                }
                if (!RegExp("^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+.[a-z]")
                    .hasMatch(value)) {
                  return 'Please a valid Email';
                }
                return null;
              },
            ),
          ),
          Padding(
            padding: const EdgeInsets.only(
                left: 40.0, right: 40.0, top: 15, bottom: 10),
            child: TextFormField(
              controller: usernameController,
              keyboardType: TextInputType.text,
              decoration: const InputDecoration(
                  prefixIcon: Icon(
                    Icons.person,
                  ),
                  //border: OutlineInputBorder(), //labelText: 'Password',
                  hintText: 'Username',
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
              //controller: _password,
              controller: passwordController,
              obscureText: isPasswordVisible,
              keyboardType: TextInputType.text,
              decoration: InputDecoration(
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
                        color: Colors.grey,
                      ),
                    ),
                  ),
                  prefixIcon: const Icon(Icons.lock),
                  //border: const OutlineInputBorder(), //labelText: 'Password',
                  hintText: 'Password',
                  hintStyle: const TextStyle(fontSize: 14)),
              validator: (value) {
                if (value!.isEmpty) {
                  return 'Please Enter a password';
                }
                if (value.length < 8) {
                  return 'Please Enter at least 8 characters';
                }
                return null;
              },
            ),
          ),
          //print(passwordController.text);
          Padding(
            padding: const EdgeInsets.only(
                left: 40.0, right: 40.0, top: 15, bottom: 10),
            child: TextFormField(
              controller: confirmpasswordController,
              keyboardType: TextInputType.text,
              obscureText: isPasswordVisible,
              decoration: InputDecoration(
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
                        color: Colors.grey,
                      ),
                    ),
                  ),
                  prefixIcon: const Icon(Icons.lock),
                  //border: const OutlineInputBorder(), //labelText: 'Password',
                  hintText: 'confirmPassword',
                  hintStyle: const TextStyle(fontSize: 14)),
              validator: (value) {
                if (value!.isEmpty) {
                  return 'Please re-enter password';
                }
                if (passwordController.text != confirmpasswordController.text) {
                  return "Password does not match";
                }
                return null;
              },
            ),
          ),
          const Padding(padding: EdgeInsets.fromLTRB(40, 10, 40, 10)),
          ElevatedButton(
              style: ElevatedButton.styleFrom(
                  minimumSize: const Size(290, 0),
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(24.0))),
              child: const Padding(
                padding: EdgeInsets.fromLTRB(20, 10, 20, 10),
                child: Text(
                  "Register",
                  style: TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: Colors.white,
                  ),
                ),
              ),
              onPressed: () {
                if (_formkey.currentState!.validate()) {
                  //print("successful");
                  setState(() {
                    register = RegisterAccount(_register());
                  });
                }
                ;
              }),
          const SizedBox(
            height: 30,
          ),
          //const Text('New User? create account'),
          Text.rich(TextSpan(children: [
            const TextSpan(text: "Have already account? "),
            TextSpan(
              text: 'Sign in',
              recognizer: TapGestureRecognizer()
                ..onTap = () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (context) => const LoginDemo()),
                  );
                },
              style: const TextStyle(fontWeight: FontWeight.bold),
            ),
          ]))
        ],
      ),
    );
  }

  FutureBuilder<registerInfo> checkRegisterRespons() {
    return FutureBuilder<registerInfo>(
        future: register,
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            if (snapshot.data!.status == true) {
              SchedulerBinding.instance.addPostFrameCallback((_) {
                Navigator.push(
                    context, MaterialPageRoute(builder: (_) => LoginDemo()));
                showDialog(
                    context: context,
                    builder: (BuildContext context) => AlertDialog(
                          title: const Text(
                              'Your registeration has been Successfully'),
                          actions: <Widget>[
                            TextButton(
                              onPressed: () => Navigator.pop(context, 'ok'),
                              child: const Text('Ok'),
                            ),
                          ],
                        ));
              });
            } else if (snapshot.data!.status == false) {
              SchedulerBinding.instance.addPostFrameCallback((_) {
                setState(() {
                  erorrMessage = snapshot.data!.message;
                  register = null;
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
