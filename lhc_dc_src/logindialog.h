#ifndef LOGINDIALOG_H
#define LOGINDIALOG_H

#include "logindialogbase.h"


class LoginDialog : public LoginDialogBase
{
	Q_OBJECT
		
	public:
		LoginDialog(QWidget *parent = 0,bool canautologin = false);
		~LoginDialog();

    static void LoginCheckedCallback(void* pt2Object, QByteArray result);
    void canContinue();

	private slots:
		virtual void on_okButton_clicked();
		virtual void on_cancelButton_clicked();
        

	private:
    QString lgUserName,       ///< Vartotojo tapatybës vardas. 
            lgUserPassword,   ///< Vartotojo tapatybës slaptaþodis.
            lgHost;           ///< Serverio adresas.

           
};

#endif
