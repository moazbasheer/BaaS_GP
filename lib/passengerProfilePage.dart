import 'package:flutter/material.dart';
import 'package:learningdart/Login.dart';
import 'package:learningdart/SearchOrganizationTrips.dart';
import 'package:learningdart/joinedTripsComplaints.dart';
import 'package:learningdart/searchPublicTrips.dart';
import 'package:learningdart/walletOption.dart';

class passengerProfilePage extends StatelessWidget {
  final String? username;
  final String? email;
  final String? rolename;
  const passengerProfilePage(
      {Key? key, this.email, this.username, this.rolename})
      : super(key: key);
  @override
  Widget build(BuildContext context) {
    return Drawer(
      child: ListView(
        controller: ScrollController(),
        // Remove padding
        padding: EdgeInsets.zero,
        children: [
          UserAccountsDrawerHeader(
            accountName: Text('$username',
                style: TextStyle(fontWeight: FontWeight.bold)),
            accountEmail: Text('$email'),
            currentAccountPicture: CircleAvatar(
              child: ClipOval(
                child: Image.asset(
                  'images/img_103236.png',
                  width: 100,
                  height: 100,
                  fit: BoxFit.fill,
                ),
              ),
            ),
            decoration: BoxDecoration(
              color: Colors.blue,
            ),
          ),
          ListTile(
            leading: const Icon(Icons.wallet_travel_rounded),
            title: const Text('Wallet'),
            // ignore: avoid_returning_null_for_void
            onTap: () => {
              Navigator.push(
                context,
                MaterialPageRoute(
                    builder: (context) => wallet(
                          email: email,
                          username: username,
                          rolename: rolename,
                        )),
              )
            },
          ),
          ListTile(
            leading: const Icon(Icons.mode_edit),
            title: const Text('Complaints'),
            // ignore: avoid_returning_null_for_void
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                    builder: (context) => joinedTripscomplaints(
                          email: email,
                          username: username,
                          rolename: rolename,
                        )),
              );
            },
          ),
          Divider(),
          ListTile(
            leading: const Icon(Icons.cable_rounded),
            title: const Text('show Organization trips'),
            // ignore: avoid_returning_null_for_void
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                    builder: (context) => showOrganizationTrips(
                          email: email,
                          name: username,
                          rolename: rolename,
                        )),
              );
            },
          ),
          ListTile(
            leading: const Icon(Icons.search),
            title: const Text('search public trips'),
            // ignore: avoid_returning_null_for_void
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                    builder: (context) => searchPublicTrips(
                          rolename: rolename,
                          email: email,
                          username: username,
                        )),
              );
            },
          ),
          const Divider(),
          ListTile(
            title: const Text('Log out'),
            leading: const Icon(Icons.logout),
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => const LoginDemo()),
              );
            },
          ),
        ],
      ),
    );
  }
}
