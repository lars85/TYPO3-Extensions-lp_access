.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _typoscript:

===========
Integrators
===========

Change days and hours in the "Access Hours" field
=================================================

The extension provides PageTSConfig/UserTSConfig to adjust the table columns and rows:

::

	tx_lpaccess {
		# Use "days := removeFromList(6,7)" to remove days from list
		days = 1,2,3,4,5,6,7
		# Use "hours := removeFromList(0,1,2,3,4,5,6,7,19,20,21,22,23)" to remove hours from list
		hours = 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23
	}

..