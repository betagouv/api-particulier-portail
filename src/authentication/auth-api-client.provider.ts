import { ConfigService } from '@nestjs/config';
import { Issuer } from 'openid-client';

export const authApiClientProvider = {
  provide: 'AuthApiClient',
  async useFactory(configService: ConfigService) {
    const issuer = await Issuer.discover(
      configService.get<string>('AUTH_API_OPENID_CONFIGURATION'),
    );

    return new issuer.Client({
      client_id: configService.get<string>('CLIENT_ID'),
      client_secret: configService.get<string>('CLIENT_SECRET'),
      redirect_uris: [`${configService.get<string>('BASE_URL')}/callback`],
      response_types: ['code'],
    });
  },
  inject: [ConfigService],
};
