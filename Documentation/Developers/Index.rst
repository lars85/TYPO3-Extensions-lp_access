.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _typoscript:

==========
Developers
==========

Add "Access Hours" field to other tables
========================================

Add this php line to ext_tables.php:

::

	\LarsPeipmann\LpAccess\Service\TCAService::addHoursFieldToTable('table_name');

..