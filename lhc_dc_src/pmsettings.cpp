#include <QFile>

#include <QString>
#include <QtXml/QDomDocument>
#include <QtGui>


#include "pmsettings.h"

const QString PMSettings::filename = "settings.xml";

PMSettings::PMSettings()
{
	doc = new QDomDocument;
	LoadSettings();
};

PMSettings::~PMSettings()
{

} 

void PMSettings::LoadSettings( )
{		
	 QFile file(qApp->applicationDirPath()+"/"+PMSettings::filename);
	 if (!file.open(QIODevice::ReadOnly))
	 {
		 	qDebug("cannot load settings file");
		 return;
	 }
 
	 if (!doc->setContent(&file)) 
	 {
		 qDebug(file.readAll().toBase64());
		 QMessageBox::warning(NULL, "Warning",
                             PMSettings::filename,
                             "&OK", QString::null , 0, 0, 1);

		 file.close();
		 return;
	 }
	 file.close();
};

bool PMSettings::sync()
{
	QByteArray xml = doc->toByteArray(); 

	//qDebug("Document source new - %s",doc->toString().toStdString().c_str());


	QFile fileOut(qApp->applicationDirPath()+"/"+PMSettings::filename);
    if (!fileOut.open(QIODevice::WriteOnly)) 
    {
		return false;
    }

	fileOut.write(xml);
	fileOut.close();

	return true;

}

void PMSettings::setAttribute(QString elementName, QString attributeValue)
{	
	QDomElement root = doc->firstChildElement("pmsettings");

    QDomElement oldTitleElement = root.firstChildElement(elementName);
    QDomElement newTitleElement = doc->createElement(elementName);

    QDomText newTitleText = doc->createTextNode(attributeValue);
    newTitleElement.appendChild(newTitleText);
    root.replaceChild(newTitleElement, oldTitleElement);			
	
};


QString PMSettings::getAttributeSettings(QString attributeSettings)
{
	QDomElement root = doc->firstChildElement("pmsettings");
	QString result = root.firstChildElement(attributeSettings).toElement().text();

	return result;
};
