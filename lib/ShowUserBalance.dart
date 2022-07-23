import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:learningdart/Login.dart';
import 'package:learningdart/PaymentController.dart';
import 'package:learningdart/walletOption.dart';

import 'package:http/http.dart' as http;

Future<balanceInfo> checkBalance() async {
  final response;
  if (rolename == "client") {
    response = await http.get(
      Uri.parse('http://baas-gp.herokuapp.com/api/client/wallet'),
      headers: {
        'Content-Type': 'application/json; charset=UTF-8',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
    );
  } else {
    response = await http.get(
      Uri.parse('http://baas-gp.herokuapp.com/api/passenger/wallet'),
      headers: {
        'Content-Type': 'application/json; charset=UTF-8',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );
  }
  if (response.statusCode == 200) {
    return balanceInfo.fromJson(jsonDecode(response.body));
  } else {
    throw Exception('Failed to get the user balance');
  }
}

class balanceInfo {
  final bool status;
  final int balance;
  const balanceInfo({required this.status, required this.balance});

  factory balanceInfo.fromJson(Map<String, dynamic> json) {
    return balanceInfo(
      status: json['status'],
      balance: json['message']['balance'],
    );
  }
}

class ShowBalance extends StatefulWidget {
  final String? email;
  final String? rolename;
  final String? username;
  const ShowBalance({Key? key, this.email, this.rolename, this.username})
      : super(key: key);
  @override
  State<ShowBalance> createState() => ShowBalanceState();
}

class ShowBalanceState extends State<ShowBalance> {
  @override
  void initState() {
    super.initState();
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
                          builder: (context) => wallet(
                                email: widget.email,
                                username: widget.username,
                                rolename: widget.rolename,
                              )),
                    );
                  }),
              title: Text(
                "Wallet's Balance",
                style: TextStyle(fontSize: 25),
              ),
            ),
            body: SingleChildScrollView(
                controller: ScrollController(),
                child: Container(
                  padding: EdgeInsets.fromLTRB(10, 30, 10, 10),
                  height: 250,
                  width: 340.0,
                  child: Card(
                    elevation: 3,
                    child: Padding(
                      padding: EdgeInsets.all(7),
                      child: Stack(children: <Widget>[
                        Align(
                          alignment: Alignment.centerRight,
                          child: Stack(
                            children: <Widget>[
                              Padding(
                                  padding:
                                      const EdgeInsets.only(left: 15, top: 5),
                                  child: Column(
                                    children: <Widget>[
                                      Row(
                                        children: <Widget>[
                                          cryptoNameSymbol(),
                                          //: Center(child: CircularProgressIndicator());
                                          SizedBox(
                                            width: 10,
                                          ),
                                        ],
                                      ),
                                      Row(
                                        children: <Widget>[
                                          FutureBuilder<balanceInfo>(
                                              future: checkBalance(),
                                              builder: (context, snapshot) {
                                                if (snapshot.hasData) {
                                                  return cryptoAmount(
                                                      snapshot.data!.balance);
                                                }
                                                return CircularProgressIndicator();
                                              }),
                                        ],
                                      ),
                                      SizedBox(
                                        height: 10,
                                      ),
                                      Row(
                                        children: [
                                          ElevatedButton(
                                            style: ElevatedButton.styleFrom(
                                              shape: RoundedRectangleBorder(
                                                borderRadius:
                                                    BorderRadius.circular(8.0),
                                              ),
                                              primary: Colors.black,
                                            ),
                                            child: Container(
                                              margin: const EdgeInsets.all(10),
                                              child: const Text(
                                                'make charging',
                                                style: TextStyle(
                                                  color: Colors.white,
                                                  fontFamily: 'halter',
                                                  fontSize: 14,
                                                ),
                                              ),
                                            ),
                                            onPressed: () => {
                                              Navigator.push(
                                                context,
                                                MaterialPageRoute(
                                                    builder: (context) =>
                                                        CreditCardPage(
                                                          email: widget.email,
                                                          username:
                                                              widget.username,
                                                          rolename:
                                                              widget.rolename,
                                                        )),
                                              )
                                            },
                                          ),
                                        ],
                                      )
                                    ],
                                  ))
                            ],
                          ),
                        )
                      ]),
                    ),
                  ),
                ))));
  }

  Widget cryptoNameSymbol() {
    return Align(
      alignment: Alignment.centerLeft,
      child: RichText(
        text: TextSpan(
          text: 'Your Balance',
          style: TextStyle(
              fontWeight: FontWeight.bold, color: Colors.grey, fontSize: 40),
        ),
      ),
    );
  }

  Widget cryptoAmount(var balance) {
    return Align(
      alignment: Alignment.centerLeft,
      child: Row(
        children: <Widget>[
          RichText(
            textAlign: TextAlign.left,
            text: TextSpan(
              text: '\n$balance EGY',
              style: TextStyle(
                  color: Colors.black,
                  fontSize: 30,
                  fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }
}
