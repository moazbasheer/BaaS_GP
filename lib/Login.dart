import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:learningdart/ForgotPassword.dart';
import 'Register.dart';
import 'widgets/header_widget.dart';

class LoginDemo extends StatefulWidget {
  const LoginDemo({Key? key}) : super(key: key);

  @override
  _LoginDemoState createState() => _LoginDemoState();
}

class _LoginDemoState extends State<LoginDemo> {
  bool isPasswordVisible = true;
  @override
  Widget build(BuildContext context) {
    double _headerHeight = 200;
    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView(
        child: Column(
          children: <Widget>[
            SizedBox(
              height: _headerHeight,
              child: HeaderWidget(_headerHeight, true, Icons.password_rounded),
            ),
            Padding(
              padding: const EdgeInsets.only(top: 60.0),
              child: Column(
                children: <Widget>[
                  SizedBox(
                      width: 200,
                      height: 100,
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
            const Padding(
              padding:
                  EdgeInsets.only(left: 40.0, right: 40.0, top: 15, bottom: 10),
              child: TextField(
                keyboardType: TextInputType.text,
                decoration: InputDecoration(
                    prefixIcon: Icon(Icons.person),
                    //border: OutlineInputBorder(),
                    //labelText: 'Email',
                    hintText: 'Username or Email',
                    hintStyle: TextStyle(fontSize: 14)),
              ),
            ),
            Padding(
              padding: const EdgeInsets.only(
                  left: 40.0, right: 40.0, top: 15, bottom: 10),
              child: TextFormField(
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
                    //border: const OutlineInputBorder(), //labelText: 'Password',
                    hintText: 'Password',
                    hintStyle: const TextStyle(fontSize: 14)),
              ),
            ),
            FlatButton(
              onPressed: () {
                Navigator.push(
                    context,
                    MaterialPageRoute(
                        builder: (_) => const ForgotPasswordPage()));
              },
              child: const Text(
                'Forgot Password',
                style: TextStyle(color: Colors.black, fontSize: 15),
              ),
            ),
            ElevatedButton(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(20, 10, 20, 10),
                child: Text(
                  "Login",
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: Colors.white,
                  ),
                ),
              ),
              onPressed: () {},
            ),
            const SizedBox(
              height: 150,
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
            /*GestureDetector(
              onTap: () {
                Navigator.push(context,
                    CupertinoPageRoute(builder: (context) => RegisterDemo()));
              },
            ),*/
          ],
        ),
      ),
    );
  }
}
