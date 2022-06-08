import 'package:flutter/material.dart';
import 'package:learningdart/Login.dart';
import 'package:learningdart/complaints.dart';
import 'package:learningdart/main.dart';
import 'package:learningdart/Complaint.dart';

class NavBar extends StatelessWidget {
  const NavBar({Key? key}) : super(key: key);
  // ignore: constant_identifier_names
  //static const IconData view_module =
  //IconData(0xe6b6, fontFamily: 'MaterialIcons');
  static const IconData walleticon =
      IconData(0xf07d4, fontFamily: 'MaterialIcons');
  @override
  Widget build(BuildContext context) {
    return Drawer(
      child: ListView(
        // Remove padding
        padding: EdgeInsets.zero,
        children: [
          UserAccountsDrawerHeader(
            accountName: const Text('Mohamed Mohsen',
                style: TextStyle(fontWeight: FontWeight.bold)),
            accountEmail: const Text('MohamedMohsen46saeed@gmail.com'),
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
            leading: const Icon(Icons.message),
            title: const Text('Messages'),
            // ignore: avoid_returning_null_for_void
            onTap: () => null,
          ),
          ListTile(
            leading: const Icon(Icons.wallet_travel_rounded),
            title: const Text('Wallet'),
            // ignore: avoid_returning_null_for_void
            onTap: () => null,
          ),
          ListTile(
            leading: const Icon(Icons.mode_edit),
            title: const Text('Complaints'),
            // ignore: avoid_returning_null_for_void
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                    builder: (context) => const MyStatefulWidget()),
              );
            },
          ),
          ListTile(
            leading: const Icon(Icons.cable_rounded),
            title: const Text('Organization trips'),
            // ignore: avoid_returning_null_for_void
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => const Complaints()),
              );
            },
          ),
          ListTile(
            leading: const Icon(Icons.punch_clock_sharp),
            title: const Text('show trips'),
            // ignore: avoid_returning_null_for_void
            onTap: () => null,
          ),
          const ListTile(
            leading: Icon(Icons.notifications),
            title: Text('Notification'),
          ),
          const Divider(),
          const ListTile(
              leading: Icon(Icons.settings), title: Text('Settings')),
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
          /*ListTile(
            leading: Icon(view_module),
            onTap: () => null,
          )*/
        ],
      ),
    );
  }
}
