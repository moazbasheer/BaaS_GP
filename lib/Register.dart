// ignore_for_file: file_names

import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:learningdart/app_api.dart';
import 'Login.dart';
import 'widgets/header_widget.dart';
import 'package:http/http.dart' as http;

class RegisterDemo extends StatefulWidget {
  const RegisterDemo({Key? key}) : super(key: key);

  @override
  _RegisterDemoState createState() => _RegisterDemoState();
}

class _RegisterDemoState extends State<RegisterDemo> {
  bool isPasswordVisible = true;
  bool checkboxValue = false;
  final GlobalKey<FormState> _formkey = GlobalKey<FormState>();
  TextEditingController firstnameController = TextEditingController();
  TextEditingController lastnameController = TextEditingController();
  TextEditingController usernameController = TextEditingController();
  TextEditingController phonenumberController = TextEditingController();
  TextEditingController emailController = TextEditingController();
  TextEditingController passwordController = TextEditingController();
  TextEditingController confirmpasswordController = TextEditingController();

  Future _register() async {
    Map data = {
      'email': emailController.text,
      'password': passwordController.text,
      'role_name': 'client',
      'phone_number': phonenumberController.text,
      'first_name': firstnameController.text,
      'last_name': lastnameController.text,
      'username': usernameController.text,
    };
    http.Response response = CallApi().postData(data, 'register');
    // ignore: unused_local_variable
    var body = json.decode(response.body);
    //print('${body}');
  }

  @override
  Widget build(BuildContext context) {
    double _headerHeight = 200;
    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView(
        child: Form(
          key: _formkey,
          child: Column(
            children: <Widget>[
              SizedBox(
                height: _headerHeight,
                child:
                    HeaderWidget(_headerHeight, true, Icons.password_rounded),
              ),
              Padding(
                padding: const EdgeInsets.only(top: 60.0),
                child: Column(
                  children: <Widget>[
                    SizedBox(
                        width: 200,
                        height: 150,
                        /*decoration: BoxDecoration(
                        color: Colors.red,
                        borderRadius: BorderRadius.circular(50.0)),*/
                        child: Image.asset(
                          'images/favpng_tour-bus-service-coach-clip-art.png',
                          fit: BoxFit.fitWidth,
                          color: Colors.white.withOpacity(1),
                          colorBlendMode: BlendMode.modulate,
                        )),
                  ],
                ),
              ),
              Padding(
                padding: const EdgeInsets.only(
                    left: 40.0, right: 40.0, top: 15, bottom: 10),
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
                  controller: phonenumberController,
                  keyboardType: TextInputType.phone,
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
                    if (value.length < 6) {
                      return 'Please Enter at least 6 characters';
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
                    if (passwordController.text !=
                        confirmpasswordController.text) {
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
                    _register();
                    Navigator.push(context,
                        MaterialPageRoute(builder: (_) => const LoginDemo()));
                    return;
                  } else {
                    //print("UnSuccessfull");
                  }
                },
              ),
              const SizedBox(
                height: 130,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
