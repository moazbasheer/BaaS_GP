import 'package:flutter/material.dart';
import 'package:learningdart/PaymentController.dart';
import 'package:learningdart/ShowUserBalance.dart';
import 'package:learningdart/clientProfilePage.dart';
import 'passengerProfilePage.dart';

class wallet extends StatefulWidget {
  final String? username;
  final String? rolename;
  final String? email;
  const wallet({Key? key, this.email, this.username, this.rolename})
      : super(key: key);
  @override
  State<wallet> createState() => _walletOptionsState();
}

class _walletOptionsState extends State<wallet> {
  Widget buildChild() {
    if (widget.rolename == "client") {
      return clientProfilePage(
        email: widget.email,
        username: widget.username,
        rolename: widget.rolename,
      );
    } else {
      return passengerProfilePage(
        email: widget.email,
        username: widget.username,
        rolename: widget.rolename,
      );
    }
  }

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        drawer: buildChild(),
        appBar: AppBar(
          title: Text(
            "Wallet",
            style: TextStyle(fontSize: 30),
          ),
        ),
        body: ListTileTheme(
          child: ListView(
            controller: ScrollController(),
            children: [
              ListTile(
                title: const Text(
                  'Charge your Wallet',
                  style: TextStyle(fontSize: 18),
                ),
                // ignore: avoid_returning_null_for_void
                onTap: () => {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                        builder: (context) => CreditCardPage(
                              email: widget.email,
                              username: widget.username,
                              rolename: widget.rolename,
                            )),
                  )
                },
              ),
              ListTile(
                title: const Text(
                  'Show your balance',
                  style: TextStyle(fontSize: 18),
                ),
                onTap: () => {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                        builder: (context) => ShowBalance(
                              email: widget.email,
                              username: widget.username,
                              rolename: widget.rolename,
                            )),
                  )
                },
              ),
            ],
          ),
        ));
  }
}
