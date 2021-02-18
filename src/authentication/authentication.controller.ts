import {
  Controller,
  Get,
  HttpService,
  Inject,
  Req,
  Res,
  Session,
} from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { Request, Response } from 'express';
import { Client, generators } from 'openid-client';

type GraviteeTokenSet = {
  token: string;
};

@Controller()
export class AuthenticationController {
  private readonly CALLBACK_URL: string;
  constructor(
    private readonly httpService: HttpService,
    private readonly configService: ConfigService,
    @Inject('AuthApiClient') private readonly authApiClient: Client,
  ) {
    this.CALLBACK_URL = `${this.configService.get<string>(
      'BASE_URL',
    )}/callback`;
  }

  @Get('/login')
  async login(@Res() res: Response, @Session() session: Record<string, any>) {
    const codeVerifier = generators.codeVerifier();
    const codeChallenge = generators.codeChallenge(codeVerifier);
    const nonce = generators.nonce();

    session.codeVerifier = codeVerifier;
    session.codeChallenge = codeChallenge;
    session.nonce = nonce;

    res.redirect(
      this.authApiClient.authorizationUrl({
        scope: 'openid email profile',
        code_challenge: codeChallenge,
        code_challenge_method: 'S256',
        redirect_uri: this.CALLBACK_URL,
        nonce: nonce,
      }),
    );
  }

  @Get('/callback')
  async callback(
    @Req() req: Request,
    @Session() session: Record<string, any>,
    @Res() res: Response,
  ) {
    const params = this.authApiClient.callbackParams(req);
    const tokenSet = await this.authApiClient.callback(
      this.CALLBACK_URL,
      params,
      {
        code_verifier: session.codeVerifier,
        nonce: session.nonce,
      },
    );

    const graviteeExchangeResponse = await this.httpService
      .post<GraviteeTokenSet>(
        this.configService.get<string>('TOKEN_EXCHANGE_ENDPOINT'),
        null,
        {
          params: {
            token: tokenSet.access_token,
          },
        },
      )
      .toPromise();

    delete session.codeChallenge;
    delete session.codeVerifier;
    delete session.nonce;

    const token = graviteeExchangeResponse.data.token;
    session.token = token;

    res.redirect('/');
  }
}
