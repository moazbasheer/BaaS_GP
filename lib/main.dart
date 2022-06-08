import 'package:flutter/material.dart';
import 'package:learningdart/Login.dart';
import 'package:learningdart/complaints.dart';
import 'package:learningdart/Complaint.dart';
//import 'package:flutter_polyline_points/flutter_polyline_points.dart';

import 'ProfilePage.dart';
import 'Splash_Screen.dart';
import 'map.dart';
//import 'Splash_Screen.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({Key? key}) : super(key: key);
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'flutter google Maps',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        primaryColor: Colors.white,
      ),
      home: const Complaints(),
      //home: MapScreen(),
    );
  }
}

/*class MyHomePage extends StatefulWidget {
  const MyHomePage({Key? key}) : super(key: key);

  @override
  State<MyHomePage> createState() => _MyHomePageState();
}

class _MyHomePageState extends State<MyHomePage> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      //body: Complaints(),
      
      //body: LoginDemo(),
      //drawer: const NavBar(),
      //appBar: AppBar(),
      //body: MapScreen(),
    );
  }
}*/
