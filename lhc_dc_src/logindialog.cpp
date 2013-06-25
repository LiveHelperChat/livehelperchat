#include "logindialog.h"
#include "pmsettings.h"
#include "webservice.h"

#include <QScriptEngine>
#include <QScriptValueIterator>
#include <QtGui>

LoginDialog::LoginDialog(QWidget *parent,bool canautologin) : LoginDialogBase(parent)
{
	PMSettings *pmsettings = new PMSettings();

	ui.PasswordEdit->setText(pmsettings->getAttributeSettings("password"));
	ui.HostEdit->setText(pmsettings->getAttributeSettings("host"));
	ui.UsernameEdit->setText(pmsettings->getAttributeSettings("username"));

	if (pmsettings->getAttributeSettings("autologin") == "true")
    {
        ui.AutoLogincheckBox->setChecked(true);
        delete pmsettings;

        if (canautologin == true) canContinue();
       
    } else {
        delete pmsettings;
    }

	

}

LoginDialog::~LoginDialog()
{

}

void LoginDialog::on_cancelButton_clicked()
{
    QDialog::reject();
}

void LoginDialog::canContinue()
{   
        PMSettings *pmsettings = new PMSettings();
        if (pmsettings->getAttributeSettings("autologin") == "true")
        {       
            LhcWebServiceClient *lhwsc = LhcWebServiceClient::instance();
            lhwsc->setFetchURL(ui.HostEdit->text().replace(QString("index.php"), QString("")).replace(QString("http://"), QString("")));
            delete pmsettings;

            QStringList filter;
            filter.append("username="+ui.UsernameEdit->text());    
            filter.append("password="+ui.PasswordEdit->text());
                       
            lhwsc->setLogins(ui.UsernameEdit->text(),ui.PasswordEdit->text());
            lhwsc->LhcSendRequestAuthorization(filter,"/xml/checklogin/",(QObject*) this, LoginDialog::LoginCheckedCallback);	
        }		
         
}

void LoginDialog::LoginCheckedCallback(void* pt2Object, QByteArray result)
{
    LoginDialog* mySelf = (LoginDialog*) pt2Object;

    QScriptValue sc; 
    QScriptEngine engine;
    sc = engine.evaluate("(" +QString(result)+ ")");
 
    if (sc.property("result").toBoolean() == true)
    {       
        mySelf->accept();
    } else {
        QMessageBox::warning(NULL, tr("Authentication failed"),
									 tr("Authentication failed"),
									 tr("&OK"), QString::null , 0, 0, 1);
    }
}

void LoginDialog::on_okButton_clicked()
{
	lgUserName = ui.UsernameEdit->text();
	
	if (!lgUserName.isEmpty())
	{	
		
			lgUserPassword = ui.PasswordEdit->text();
			
			if (!lgUserPassword.isEmpty())
			{
                lgHost = ui.HostEdit->text().replace(QString("index.php"), QString("")).replace(QString("http://"), QString(""));
			
				if (!lgHost.isEmpty())
				{
						PMSettings *pmsettings = new PMSettings();						
						pmsettings->setAttribute("username",lgUserName);
						pmsettings->setAttribute("password",lgUserPassword);
						pmsettings->setAttribute("host",lgHost);
					
						if (ui.AutoLogincheckBox->isChecked())
							pmsettings->setAttribute("autologin","true");
						else
							pmsettings->setAttribute("autologin","false");
						

						pmsettings->sync();

						delete pmsettings;

                        LhcWebServiceClient *lhwsc = LhcWebServiceClient::instance();
                        lhwsc->setFetchURL(lgHost);
                 
                        QStringList filter;
                        filter.append("username="+lgUserName);    
                        filter.append("password="+lgUserPassword);

                        lhwsc->setLogins(lgUserName,lgUserPassword);
                        lhwsc->LhcSendRequestAuthorization(filter,"/xml/checklogin/",(QObject*) this, LoginDialog::LoginCheckedCallback);
                        		
				}else { 
					QMessageBox::warning(this, tr("Warning"),
								 tr("The host field is empty!"),
								 tr("&OK"), QString::null , 0, 0, 1);
					ui.HostEdit->setFocus();  
				}

			}else { 
                QMessageBox::warning(this, tr("Warning"),
								 tr("The password field is empty!"),
								 tr("&OK"), QString::null , 0, 0, 1);
				ui.PasswordEdit->setFocus();  
			}
		

    } else {
		QMessageBox::warning(this, tr("Warning"),
                             tr("The user name field is empty!"),
                             tr("&OK"), QString::null , 0, 0, 1);
        ui.UsernameEdit->setFocus();
	}
}
