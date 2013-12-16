#ifndef PMSETTINGS_H
#define PMSETTINGS_H

#include <QString>
#include <QtXml/QDomDocument>

class PMSettings
{

public:
	QDomDocument *doc;

	/**
    * @brief Isssaugomas failas á diska.
    *
    * @author Remigijus Kiminas 
	*/
	bool sync();

	/**
    * @brief Nustatomas norimas elemento pavadinimas ir reiksme
    *
    * @author Remigijus Kiminas */
	void setAttribute(QString elementName, QString attributeValue);

	/**
    * @brief Gaunamas konkretus atributas is parametrø failo.
    *
    * @author Remigijus Kiminas
    **/
	QString getAttributeSettings(QString attributeSettings);

	/**
    * @brief Klasës konstruktorius.
    *
    * @author Remigijus Kiminas
    **/ 
    PMSettings();

	/**
    * @brief Klasës konstruktorius.
    *
    * @author Remigijus Kiminas
    **/ 
	void LoadSettings();

	/**
    * @brief Klasës destruktorius.
    *
    * @author Remigijus Kiminas
    **/
	~PMSettings();

private:

	
	static const QString filename; ///< Failas i kuri saugoma

	

   

};

#endif
