#include <QApplication>
#include <QHBoxLayout>
#include <QSlider>
#include <QSpinBox>
#include <QtGui>
#include <QTranslator>

#include "mainwindow.h"
#include "pmsettings.h"
#include "logindialog.h"

int main(int argc, char *argv[])
{
QApplication app(argc, argv);

qApp->addLibraryPath( qApp->applicationDirPath() + "/plugins");

QTranslator translator;

PMSettings *pmsettings = new PMSettings();
translator.load("translations/lhc_"+pmsettings->getAttributeSettings("language")+".qm");
delete pmsettings;

app.installTranslator(&translator);

//Inicijuojam DB
QCoreApplication::setOrganizationName("Remdex");
QCoreApplication::setOrganizationDomain("remdex.info");
QCoreApplication::setApplicationName("Live helper chat");

LoginDialog *lgnDialog = new LoginDialog(0,true);

if(!lgnDialog->exec())
{
    QTimer::singleShot(250, qApp, SLOT(quit())); 
} 

delete lgnDialog;


MainWindow w;
w.show();
app.connect(&app, SIGNAL(lastWindowClosed()), &app, SLOT(quit()));
	return app.exec();

}
