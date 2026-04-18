# Strays Worth Saving Management System  

Undergraduate Thesis — Tanauan Institute, Inc. | Bachelor of Science in Computer Science | 2025

## Introduction

This guide will walk you through how to use the Strays Worth Saving Management System.  
The system is designed to be simple and user-friendly, mainly for encoding, tracking, and monitoring data related to animals, adoptions, donations, and supplies.

## Dashboard

- Total number of animals
- Number of available animals
- Number of adopted animals
- Total donations received
- Daily rescue count
- Low supply alerts

## Animals Module

Key functions:
- Add a new animal
- Edit existing animal details
- Delete records if necessary
- View the list of animals

Important:
Each animal must have the correct status:
- Available — ready for adoption
- Adopted — already adopted
- Under Care — still being treated or monitored

## Adoptions Module

Steps:
1. Select the animal
2. Enter adopter details
3. Save the record

Once saved, the system will automatically update the animal’s status to "Adopted".


## Donations Module

The Donations module is used to record all incoming donations.

Key functions:
- Add donation entries
- Track total donations
- Monitor donation trends

All recorded donations are automatically reflected on the Dashboard.

## Supplies Module

The Supplies module is used to manage inventory.

Examples of supplies:
- Vitamins
- Syringes
- Animal food

Key functions:
- Add new supply items
- Update quantities
- Monitor stock levels

If supply levels are low, they will appear in the "Low Supplies" section on the Dashboard.


## Volunteers Module

The Volunteers module is used to manage volunteer information.

Key functions:
- Add new volunteers
- Update volunteer details
- Maintain records of participants


## Records Module

The Records module serves as the system’s history log.

It allows you to review past data such as:
- Rescue records
- Adoption records
- Other system activities


## Users Module

The Users module is used to manage system access.

Key functions:
- Add new users
- Assign roles (e.g., Admin, Staff)
- Edit or deactivate user accounts

## System Workflow

1. Rescue an animal and add it to the Animals module
2. Update the animal status to "Available"
3. When adoption occurs, record it in the Adoptions module
4. The system automatically updates the status to "Adopted"
5. Record donations in the Donations module
6. Monitor and update inventory in the Supplies module

## Notes

- Always keep records updated to ensure accurate reporting
- Regularly check the Dashboard for alerts and summaries
- Avoid duplicate entries
- Ensure correct data input for all modules

## Roles & Access

| Role            | Access                                                                            |
| --------------- | --------------------------------------------------------------------------------- |
| `administrator` | Full access — animals, volunteers, adoptions, donations, supplies, records, users |
| `staff`         | animals, volunteers, adoptions, supplies, records |

**Development credentials:**

| Role          | Email                 | Password   |
| ------------- | --------------------- | ---------- |
| Administrator | `admin`               | `admin123` |
| Staff         | `staff`               | `staff123` |

Tanauan Institute, Inc. - College of Computer Science, 2026


Stray Worth Saving. All rights reserved.